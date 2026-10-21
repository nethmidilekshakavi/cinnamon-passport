<?php

namespace App\Http\Controllers;

use App\Models\StudySus;
use App\Models\StudyTrial;
use App\Services\StudyRecorder;
use App\Services\StudyScenarios;
use App\Services\StudyStats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudyController extends Controller
{
    private const SUS_ITEMS = [
        'I think that I would like to use this system frequently.',
        'I found the system unnecessarily complex.',
        'I thought the system was easy to use.',
        'I think that I would need the support of a technical person to be able to use this system.',
        'I found the various functions in this system were well integrated.',
        'I thought there was too much inconsistency in this system.',
        'I would imagine that most people would learn to use this system very quickly.',
        'I found the system very cumbersome to use.',
        'I felt very confident using the system.',
        'I needed to learn a lot of things before I could get going with this system.',
    ];

    public function index(): Response
    {
        return Inertia::render('Study/Index', [
            'trials' => StudyTrial::latest('id')->take(10)->get(),
        ]);
    }

    public function store(Request $request, StudyRecorder $recorder): RedirectResponse
    {
        $data = $request->validate([
            'participant' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9_-]+$/'],
            'channel' => ['required', 'in:sms,app'],
        ]);

        $trial = $recorder->create($request->user(), strtoupper($data['participant']), $data['channel']);

        return redirect()->route('study.show', $trial);
    }

    public function show(Request $request, StudyTrial $trial): Response
    {
        abort_unless((int) $trial->user_id === $request->user()->id, 403);

        return Inertia::render('Study/Show', [
            'trial' => [
                'id' => $trial->id,
                'participant' => $trial->participant,
                'channel' => $trial->channel,
                'started' => $trial->started_at !== null,
                'text' => StudyScenarios::text($trial->scenario, $trial->trial_date),
            ],
        ]);
    }

    public function start(Request $request, StudyTrial $trial, StudyRecorder $recorder): RedirectResponse
    {
        abort_unless((int) $trial->user_id === $request->user()->id && $trial->started_at === null, 403);

        $recorder->start($trial);

        return redirect()->route($trial->channel === 'sms' ? 'sms.index' : 'batches.create');
    }

    public function giveUp(Request $request, StudyTrial $trial, StudyRecorder $recorder): RedirectResponse
    {
        abort_unless((int) $trial->user_id === $request->user()->id, 403);

        $recorder->giveUp($trial);

        return redirect()->route('study.index');
    }

    public function susForm(Request $request): Response
    {
        return Inertia::render('Study/Sus', [
            'items' => self::SUS_ITEMS,
            'participant' => $request->query('participant'),
            'channel' => $request->query('channel'),
        ]);
    }

    public function susStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'participant' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9_-]+$/'],
            'channel' => ['required', 'in:sms,app'],
            'answers' => ['required', 'array', 'size:10'],
            'answers.*' => ['required', 'integer', 'between:1,5'],
        ]);

        StudySus::updateOrCreate(
            ['participant' => strtoupper($data['participant']), 'channel' => $data['channel']],
            ['answers' => array_values($data['answers']), 'score' => StudyStats::susScore($data['answers'])],
        );

        return redirect()->route('study.results');
    }

    public function results(StudyStats $stats): Response
    {
        return Inertia::render('Study/Results', [
            'summary' => $stats->summary(),
            'trials' => StudyTrial::whereNotNull('finished_at')->orderBy('participant')->orderBy('id')->get(),
            'sus' => StudySus::orderBy('participant')->get(['participant', 'channel', 'score']),
        ]);
    }

    /** CSV for analysis in Excel / JASP / R. Use ?type=sus for the SUS scores. */
    public function export(Request $request): StreamedResponse
    {
        if ($request->query('type') === 'sus') {
            return response()->streamDownload(function () {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['participant', 'channel', 'sus_score']);
                foreach (StudySus::orderBy('participant')->get() as $s) {
                    fputcsv($out, [$s->participant, $s->channel, $s->score]);
                }
                fclose($out);
            }, 'cinnamon-study-sus.csv', ['Content-Type' => 'text/csv']);
        }

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['participant', 'channel', 'scenario', 'success', 'duration_s', 'attempts', 'failed_attempts', 'data_errors', 'started_at']);
            foreach (StudyTrial::whereNotNull('finished_at')->orderBy('participant')->orderBy('id')->get() as $t) {
                fputcsv($out, [
                    $t->participant, $t->channel, $t->scenario, $t->success ? 1 : 0,
                    $t->duration_ms !== null ? round($t->duration_ms / 1000, 1) : '',
                    $t->attempts, $t->errors, $t->data_errors, $t->started_at,
                ]);
            }
            fclose($out);
        }, 'cinnamon-study-trials.csv', ['Content-Type' => 'text/csv']);
    }
}
