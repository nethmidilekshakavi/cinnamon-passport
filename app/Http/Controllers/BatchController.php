<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBatchRequest;
use App\Models\Batch;
use App\Models\User;
use App\Services\AnomalyDetector;
use App\Services\HandoverService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\StudyRecorder;

class BatchController extends Controller
{
    public function index(Request $request): Response
    {
        $batches = $request->user()
            ->batches()
            ->with('collector:id,name')
            ->latest()
            ->get([
                'id', 'code', 'plot', 'weight_kg', 'harvest_date', 'drying_days', 'grade',
                'status', 'source', 'flags', 'collector_id', 'handover_requested_at', 'handed_over_at',
            ]);

        return Inertia::render('Batches/Index', [
            'batches' => $batches,
            'collectors' => User::where('role', User::ROLE_COLLECTOR)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Batches/Create', ['grades' => Batch::GRADES]);
    }

    public function store(StoreBatchRequest $request, AnomalyDetector $detector, StudyRecorder $study): RedirectResponse
    {
        $batch = $request->user()->batches()->create(
            $request->validated() + [
                'source' => Batch::SOURCE_APP,
                'status' => Batch::STATUS_RECORDED,
            ]
        );

        $detector->flag($batch);
        $study->succeeded($request->user(), 'app', $batch);

        return redirect()->route('batches.index');
    }

    /** App version of "HAND CIN-0001 <collector phone>". */
    public function handover(Request $request, Batch $batch, HandoverService $handover): RedirectResponse
    {
        $collectorId = $request->validate([
            'collector_id' => ['required', 'integer'],
        ])['collector_id'];

        $collector = User::where('role', User::ROLE_COLLECTOR)->find($collectorId);

        if (! $collector) {
            return back()->withErrors(['collector_id' => 'no_collector']);
        }

        $result = $handover->request($request->user(), $batch->code, $collector);

        if (! $result['ok']) {
            return back()->withErrors(['collector_id' => $result['error']]);
        }

        return back();
    }
}
