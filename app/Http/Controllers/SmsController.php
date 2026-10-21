<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AnomalyDetector;
use App\Services\HandoverService;
use App\Services\SmsParser;
use App\Services\SmsReplies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\StudyRecorder;

class SmsController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeRole($request);

        return Inertia::render('Sms/Simulator', [
            'result' => session('sms'),
            'role' => $request->user()->role,
        ]);
    }

    public function store(
        Request $request,
        SmsParser $parser,
        AnomalyDetector $detector,
        HandoverService $handover,
    ): RedirectResponse {
        $this->authorizeRole($request);

        $text = $request->validate([
            'message' => ['required', 'string', 'max:160'],
        ])['message'];

        $user = $request->user();
        $lang = SmsReplies::languageOf($text);
        $tokens = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $verb = mb_strtoupper($tokens[0] ?? '');

        if (in_array($verb, HandoverService::HAND_WORDS, true)) {
            [$ok, $reply, $code] = $this->handleHand($user, $tokens, $lang, $handover);
        } elseif (in_array($verb, HandoverService::CONFIRM_WORDS, true)) {
            [$ok, $reply, $code] = $this->handleConfirm($user, $tokens, $lang, $handover);
        } else {
            [$ok, $reply, $code] = $this->handleBatch($user, $text, $lang, $parser, $detector);
        }

        return redirect()->route('sms.index')->with('sms', [
            'sent' => $text,
            'ok' => $ok,
            'reply' => $reply,
            'code' => $code,
        ]);
    }

    private function authorizeRole(Request $request): void
    {
        abort_unless(
            in_array($request->user()->role, [User::ROLE_GROWER, User::ROLE_COLLECTOR], true),
            403
        );
    }

    /** BATCH P1 40KG 12OCT DRY3 ALBA  (growers only) */
    /** BATCH P1 40KG 12OCT DRY3 ALBA  (growers only) */
    private function handleBatch(User $user, string $text, string $lang, SmsParser $parser, AnomalyDetector $detector): array
    {
        if ($user->role !== User::ROLE_GROWER) {
            return [false, SmsReplies::handover($lang, 'role'), null];
        }

        $parsed = $parser->parse($text);

        if (! $parsed['ok']) {
            app(StudyRecorder::class)->failedAttempt($user, 'sms');

            return [false, SmsReplies::failure($lang, $parsed['errors']), null];
        }

        $batch = $user->batches()->create($parsed['data'] + [
                'source' => \App\Models\Batch::SOURCE_SMS,
                'status' => \App\Models\Batch::STATUS_RECORDED,
            ]);
        $batch->refresh();

        // Flag unusual batches (heavy, too little drying, duplicate, ...).
        $detector->flag($batch);
        app(StudyRecorder::class)->succeeded($user, 'sms', $batch);

        return [true, SmsReplies::success($lang, $batch), $batch->code];
    }

    /** HAND CIN-0001 0771234567  (growers only) */
    private function handleHand(User $user, array $tokens, string $lang, HandoverService $handover): array
    {
        if ($user->role !== User::ROLE_GROWER) {
            return [false, SmsReplies::handover($lang, 'role'), null];
        }

        $code = $tokens[1] ?? null;
        $phone = $tokens[2] ?? null;

        if (! $code || ! $phone) {
            return [false, SmsReplies::handover($lang, 'format_hand'), null];
        }

        $collector = $handover->findCollectorByPhone($phone);

        if (! $collector) {
            return [false, SmsReplies::handover($lang, 'no_collector'), null];
        }

        $result = $handover->request($user, $code, $collector);

        if (! $result['ok']) {
            return [false, SmsReplies::handover($lang, $result['error'], ['code' => strtoupper($code)]), null];
        }

        return [true, SmsReplies::handover($lang, 'requested', [
            'code' => $result['batch']->code,
            'collector' => $collector->name,
        ]), $result['batch']->code];
    }

    /** OK CIN-0001  (collectors only) */
    private function handleConfirm(User $user, array $tokens, string $lang, HandoverService $handover): array
    {
        if ($user->role !== User::ROLE_COLLECTOR) {
            return [false, SmsReplies::handover($lang, 'role'), null];
        }

        $code = $tokens[1] ?? null;

        if (! $code) {
            return [false, SmsReplies::handover($lang, 'format_ok'), null];
        }

        $result = $handover->confirm($user, $code);

        if (! $result['ok']) {
            return [false, SmsReplies::handover($lang, $result['error'], ['code' => strtoupper($code)]), null];
        }

        return [true, SmsReplies::handover($lang, 'confirmed', [
            'code' => $result['batch']->code,
            'grower' => $result['batch']->grower->name,
        ]), $result['batch']->code];
    }
}
