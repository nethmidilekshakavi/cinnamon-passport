<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Services\SmsParser;
use App\Services\SmsReplies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SmsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Sms/Simulator', [
            'result' => session('sms'),
        ]);
    }

    public function store(Request $request, SmsParser $parser): RedirectResponse
    {
        $text = $request->validate([
            'message' => ['required', 'string', 'max:160'],
        ])['message'];

        $parsed = $parser->parse($text);
        $lang = SmsReplies::languageOf($text);
        $code = null;

        if ($parsed['ok']) {
            $batch = $request->user()->batches()->create($parsed['data'] + [
                    'source' => Batch::SOURCE_SMS,
                    'status' => Batch::STATUS_RECORDED,
                ]);
            $batch->refresh();

            $code = $batch->code;
            $reply = SmsReplies::success($lang, $batch);
        } else {
            $reply = SmsReplies::failure($lang, $parsed['errors']);
        }

        return redirect()->route('sms.index')->with('sms', [
            'sent' => $text,
            'ok' => $parsed['ok'],
            'reply' => $reply,
            'code' => $code,
        ]);
    }
}
