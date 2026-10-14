<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Random\Engine\Mt19937;
use Random\Randomizer;

/**
 * Builds reproducible fake batches. Some are given a known anomaly (the label),
 * which is the ground truth the detector is later scored against.
 * Ground-truth rules are written here with their own numbers on purpose,
 * not read from AnomalyDetector.
 */
class SyntheticBatchGenerator
{
    public const TYPES = [
        'WEIGHT_HIGH', 'DRYING_SHORT', 'DRYING_LONG', 'LATE_LOG', 'DUPLICATE', 'WEIGHT_JUMP',
    ];

    private Randomizer $rng;

    public function __construct(int $seed = 42)
    {
        $this->rng = new Randomizer(new Mt19937($seed));
    }

    /**
     * @return array<int, array{grower: int, plot: string, weight_kg: float, harvest_date: string, drying_days: int, label: ?string}>
     */
    public function generate(
        int $growers,
        int $batchesPerGrower,
        float $anomalyRate,
        float $noiseRate,
        CarbonImmutable $today,
    ): array {
        $all = [];

        for ($g = 1; $g <= $growers; $g++) {
            $base = $this->rng->getInt(15, 80); // this grower's typical batch weight

            $offsets = [];
            for ($i = 0; $i < $batchesPerGrower; $i++) {
                $offsets[] = $this->rng->getInt(0, 40);
            }
            rsort($offsets); // oldest harvest first

            $history = [];

            foreach ($offsets as $offset) {
                $batch = [
                    'grower' => $g,
                    'plot' => 'P'.$this->rng->getInt(1, 5),
                    'weight_kg' => round($base * $this->rng->getInt(70, 130) / 100, 1),
                    'harvest_date' => $today->subDays($offset)->toDateString(),
                    'drying_days' => $this->rng->getInt(3, 7),
                    'label' => null,
                ];

                $roll = $this->rng->getInt(0, 9999) / 10000;

                if ($roll < $anomalyRate) {
                    $batch = $this->inject($batch, $history, $today);
                } elseif ($roll < $anomalyRate + $noiseRate) {
                    $batch = $this->legitUnusual($batch);
                }

                $history[] = $batch;
                $all[] = $batch;
            }
        }

        return $all;
    }

    private function inject(array $batch, array $history, CarbonImmutable $today): array
    {
        $type = self::TYPES[$this->rng->getInt(0, count(self::TYPES) - 1)];

        if ($type === 'DUPLICATE' && $history === []) {
            $type = 'WEIGHT_HIGH';
        }

        if ($type === 'WEIGHT_JUMP') {
            $average = count($history) >= 3
                ? array_sum(array_column($history, 'weight_kg')) / count($history)
                : 0;

            if ($average <= 0 || $average * 3.5 > 190) {
                $type = 'WEIGHT_HIGH';
            } else {
                $batch['weight_kg'] = round(min(190.0, $average * $this->rng->getInt(35, 60) / 10), 1);
            }
        }

        switch ($type) {
            case 'WEIGHT_HIGH':
                $batch['weight_kg'] = (float) $this->rng->getInt(210, 400);
                break;
            case 'DRYING_SHORT':
                $batch['drying_days'] = $this->rng->getInt(1, 2);
                break;
            case 'DRYING_LONG':
                $batch['drying_days'] = $this->rng->getInt(15, 25);
                break;
            case 'LATE_LOG':
                $batch['harvest_date'] = $today->subDays($this->rng->getInt(50, 120))->toDateString();
                break;
            case 'DUPLICATE':
                $last = end($history);
                $batch['plot'] = $last['plot'];
                $batch['weight_kg'] = $last['weight_kg'];
                $batch['harvest_date'] = $last['harvest_date'];
                break;
        }

        $batch['label'] = $type;

        return $batch;
    }

    /** A legitimate batch that merely looks unusual (fast dry season, big plot). Not an anomaly. */
    private function legitUnusual(array $batch): array
    {
        if ($this->rng->getInt(0, 1) === 0) {
            $batch['drying_days'] = 2;
        } else {
            $batch['weight_kg'] = (float) $this->rng->getInt(205, 260);
        }

        return $batch;
    }
}
