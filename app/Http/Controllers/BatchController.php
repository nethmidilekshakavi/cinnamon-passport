<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBatchRequest;
use App\Models\Batch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BatchController extends Controller
{
    public function index(Request $request): Response
    {
        $batches = $request->user()
            ->batches()
            ->latest()
            ->get(['id', 'code', 'plot', 'weight_kg', 'harvest_date', 'drying_days', 'grade', 'status', 'source']);

        return Inertia::render('Batches/Index', ['batches' => $batches]);
    }

    public function create(): Response
    {
        return Inertia::render('Batches/Create', ['grades' => Batch::GRADES]);
    }

    public function store(StoreBatchRequest $request): RedirectResponse
    {
        $request->user()->batches()->create(
            $request->validated() + ['source' => Batch::SOURCE_APP]
        );

        return redirect()->route('batches.index');
    }
}
