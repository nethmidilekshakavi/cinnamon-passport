<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VerifyController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Verify/Index');
    }

    public function lookup(Request $request): RedirectResponse
    {
        $code = strtoupper(trim($request->validate([
            'code' => ['required', 'string', 'max:20'],
        ])['code']));

        if (! Batch::where('code', $code)->exists()) {
            return back()->withErrors(['code' => 'not_found']);
        }

        return redirect()->route('verify.show', $code);
    }

    public function show(string $code): Response
    {
        $batch = Batch::where('code', strtoupper($code))->firstOrFail();

        // Only what a buyer needs. No grower identity, phone or notes.
        return Inertia::render('Verify/Show', [
            'batch' => [
                'code' => $batch->code,
                'plot' => $batch->plot,
                'weight_kg' => $batch->weight_kg,
                'harvest_date' => $batch->harvest_date->toDateString(),
                'drying_days' => $batch->drying_days,
                'grade' => $batch->grade,
                'status' => $batch->status,
                'source' => $batch->source,
                'flags' => $batch->flags ?? [],
                'handed_over_at' => $batch->handed_over_at?->toDateString(),
            ],
        ]);
    }
}
