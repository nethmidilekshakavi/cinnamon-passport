<?php

use App\Services\AnomalyDetector;
use App\Services\DetectorEvaluator;
use App\Services\SyntheticBatchGenerator;
use Carbon\CarbonImmutable;

function synToday(): CarbonImmutable
{
    return CarbonImmutable::create(2026, 10, 13);
}

it('generates the same data for the same seed', function () {
    $a = (new SyntheticBatchGenerator(7))->generate(3, 5, 0.2, 0.05, synToday());
    $b = (new SyntheticBatchGenerator(7))->generate(3, 5, 0.2, 0.05, synToday());

    expect($a)->toBe($b)->and($a)->toHaveCount(15);
});

it('injects roughly the requested share of anomalies', function () {
    $batches = (new SyntheticBatchGenerator(1))->generate(100, 10, 0.2, 0.0, synToday());
    $labelled = count(array_filter($batches, fn ($b) => $b['label'] !== null));

    expect($labelled)->toBeGreaterThan(150)->toBeLessThan(250);
});

it('computes precision and recall from labelled batches', function () {
    $mk = fn (int $g, string $plot, float $kg, string $date, int $days, ?string $label) => [
        'grower' => $g, 'plot' => $plot, 'weight_kg' => $kg,
        'harvest_date' => $date, 'drying_days' => $days, 'label' => $label,
    ];

    $r = (new DetectorEvaluator(new AnomalyDetector()))->evaluate([
        $mk(1, 'P1', 40, '2026-10-12', 4, null),            // normal, not flagged  -> TN
        $mk(1, 'P2', 300, '2026-10-11', 4, 'WEIGHT_HIGH'),  // flagged              -> TP
        $mk(1, 'P3', 40, '2026-10-10', 4, 'LATE_LOG'),      // detector misses it   -> FN
        $mk(2, 'P1', 40, '2026-10-12', 2, null),            // normal but flagged   -> FP
    ], synToday());

    expect([$r['tp'], $r['fp'], $r['fn'], $r['tn']])->toBe([1, 1, 1, 1])
        ->and($r['precision'])->toBe(0.5)
        ->and($r['recall'])->toBe(0.5)
        ->and($r['false_by_rule'])->toBe(['DRYING_SHORT' => 1]);
});
