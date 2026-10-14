<?php

namespace App\Services;

use App\Models\Batch;
use Carbon\CarbonImmutable;

/**
 * Rule-based anomaly flags for a newly logged batch.
 * Thresholds are assumptions to be calibrated (see dissertation, RQ2).
 */
class AnomalyDetector
{
    public const MAX_WEIGHT_KG = 200;
    public const MIN_DRYING_DAYS = 3;
    public const MAX_DRYING_DAYS = 14;
    public const MAX_AGE_DAYS = 45;
    public const JUMP_FACTOR = 3;
    public const JUMP_MIN_HISTORY = 3;

    /**
     * @param  array{plot: string, weight_kg: float|int|string, harvest_date: string, drying_days: int}  $batch
     * @param  array<int, array{plot: string, weight_kg: float|int|string, harvest_date: string}>  $history  the grower's earlier batches
     * @return array<int, string>  rule codes that fired
     */
    public function detect(array $batch, array $history = [], ?CarbonImmutable $today = null): array
    {
        $today ??= CarbonImmutable::today();
        $flags = [];

        $weight = (float) $batch['weight_kg'];
        $days = (int) $batch['drying_days'];
        $harvest = CarbonImmutable::parse($batch['harvest_date'])->startOfDay();

        if ($weight > self::MAX_WEIGHT_KG) {
            $flags[] = 'WEIGHT_HIGH';
        }

        if ($days < self::MIN_DRYING_DAYS) {
            $flags[] = 'DRYING_SHORT';
        } elseif ($days > self::MAX_DRYING_DAYS) {
            $flags[] = 'DRYING_LONG';
        }

        if ($harvest->lt($today->subDays(self::MAX_AGE_DAYS))) {
            $flags[] = 'LATE_LOG';
        }

        foreach ($history as $old) {
            if (
                $old['plot'] === $batch['plot']
                && substr((string) $old['harvest_date'], 0, 10) === $harvest->toDateString()
                && abs((float) $old['weight_kg'] - $weight) < 0.01
            ) {
                $flags[] = 'DUPLICATE';
                break;
            }
        }

        if (count($history) >= self::JUMP_MIN_HISTORY) {
            $average = array_sum(array_map(fn ($h) => (float) $h['weight_kg'], $history)) / count($history);

            if ($weight > $average * self::JUMP_FACTOR) {
                $flags[] = 'WEIGHT_JUMP';
            }
        }

        return $flags;
    }

    /** Check a saved batch against its grower's history and store the result. */
    public function flag(Batch $batch): void
    {
        $history = Batch::where('grower_id', $batch->grower_id)
            ->where('id', '!=', $batch->id)
            ->get(['plot', 'weight_kg', 'harvest_date'])
            ->map(fn (Batch $b) => [
                'plot' => $b->plot,
                'weight_kg' => $b->weight_kg,
                'harvest_date' => $b->harvest_date->toDateString(),
            ])
            ->all();

        $flags = $this->detect([
            'plot' => $batch->plot,
            'weight_kg' => $batch->weight_kg,
            'harvest_date' => $batch->harvest_date->toDateString(),
            'drying_days' => $batch->drying_days,
        ], $history);

        $attributes = ['flags' => $flags];

        // Only touch status when something was flagged; otherwise leave it as it is.
        if ($flags) {
            $attributes['status'] = Batch::STATUS_FLAGGED;
        }

        $batch->forceFill($attributes)->saveQuietly();
    }
}
