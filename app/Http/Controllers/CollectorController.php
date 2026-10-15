<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Services\HandoverService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CollectorController extends Controller
{
    private const COLUMNS = [
        'id', 'code', 'plot', 'weight_kg', 'harvest_date', 'drying_days', 'grade',
        'status', 'flags', 'grower_id', 'handover_requested_at', 'handed_over_at',
    ];

    public function index(Request $request): Response
    {
        $me = $request->user();

        $pending = Batch::with('grower:id,name')
            ->where('collector_id', $me->id)
            ->whereNotNull('handover_requested_at')
            ->whereNull('handed_over_at')
            ->latest('handover_requested_at')
            ->get(self::COLUMNS);

        $received = Batch::with('grower:id,name')
            ->where('collector_id', $me->id)
            ->whereNotNull('handed_over_at')
            ->latest('handed_over_at')
            ->get(self::COLUMNS);

        return Inertia::render('Collector/Home', [
            'pending' => $pending,
            'received' => $received,
        ]);
    }

    public function confirm(Request $request, Batch $batch, HandoverService $handover): RedirectResponse
    {
        $handover->confirm($request->user(), $batch->code);

        return back();
    }
}
