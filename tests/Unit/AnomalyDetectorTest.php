<?php

use App\Services\AnomalyDetector;
use Carbon\CarbonImmutable;

function anomalyCheck(array $override = [], array $history = []): array
{
    $batch = array_merge([
        'plot' => 'P1',
        'weight_kg' => 40,
        'harvest_date' => '2026-10-12',
        'drying_days' => 3,
    ], $override);

    return (new AnomalyDetector())->detect($batch, $history, CarbonImmutable::create(2026, 10, 13));
}

it('does not flag a normal batch', function () {
    expect(anomalyCheck())->toBe([]);
});

it('flags a very heavy batch', function () {
    expect(anomalyCheck(['weight_kg' => 250]))->toBe(['WEIGHT_HIGH']);
});

it('flags drying that is too short or too long', function () {
    expect(anomalyCheck(['drying_days' => 2]))->toBe(['DRYING_SHORT']);
    expect(anomalyCheck(['drying_days' => 20]))->toBe(['DRYING_LONG']);
});

it('flags a batch logged long after harvest', function () {
    expect(anomalyCheck(['harvest_date' => '2026-08-01']))->toBe(['LATE_LOG']);
});

it('flags an exact duplicate', function () {
    $history = [['plot' => 'P1', 'weight_kg' => 40, 'harvest_date' => '2026-10-12']];

    expect(anomalyCheck([], $history))->toBe(['DUPLICATE']);
});

it('flags a weight jump only with enough history', function () {
    $mk = fn (int $day) => ['plot' => 'P1', 'weight_kg' => 20, 'harvest_date' => "2026-10-0{$day}"];

    expect(anomalyCheck(['weight_kg' => 100], [$mk(1), $mk(2), $mk(3)]))->toBe(['WEIGHT_JUMP']);
    expect(anomalyCheck(['weight_kg' => 100], [$mk(1), $mk(2)]))->toBe([]);
});

it('can fire several rules at once', function () {
    expect(anomalyCheck(['weight_kg' => 250, 'drying_days' => 1]))
        ->toBe(['WEIGHT_HIGH', 'DRYING_SHORT']);
});
