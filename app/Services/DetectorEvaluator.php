<?php

namespace App\Services;

use Carbon\CarbonImmutable;

/** Scores AnomalyDetector against labelled synthetic batches. */
class DetectorEvaluator
{
    public function __construct(private AnomalyDetector $detector)
    {
    }

    /**
     * @param  array<int, array>  $batches  output of SyntheticBatchGenerator::generate()
     */
    public function evaluate(array $batches, CarbonImmutable $today): array
    {
        $tp = $fp = $fn = $tn = 0;
        $history = [];
        $byType = [];
        $falseByRule = [];

        foreach (SyntheticBatchGenerator::TYPES as $type) {
            $byType[$type] = ['injected' => 0, 'detected' => 0];
        }

        foreach ($batches as $batch) {
            $grower = $batch['grower'];
            $flags = $this->detector->detect($batch, $history[$grower] ?? [], $today);
            $history[$grower][] = $batch;

            $actual = $batch['label'] !== null;
            $predicted = $flags !== [];

            if ($actual) {
                $byType[$batch['label']]['injected']++;
                if (in_array($batch['label'], $flags, true)) {
                    $byType[$batch['label']]['detected']++;
                }
            } else {
                foreach ($flags as $rule) {
                    $falseByRule[$rule] = ($falseByRule[$rule] ?? 0) + 1;
                }
            }

            match (true) {
                $actual && $predicted => $tp++,
                ! $actual && $predicted => $fp++,
                $actual && ! $predicted => $fn++,
                default => $tn++,
            };
        }

        $precision = ($tp + $fp) > 0 ? $tp / ($tp + $fp) : null;
        $recall = ($tp + $fn) > 0 ? $tp / ($tp + $fn) : null;
        $f1 = ($precision && $recall) ? 2 * $precision * $recall / ($precision + $recall) : null;

        return [
            'total' => count($batches),
            'anomalies' => $tp + $fn,
            'tp' => $tp,
            'fp' => $fp,
            'fn' => $fn,
            'tn' => $tn,
            'precision' => $precision,
            'recall' => $recall,
            'f1' => $f1,
            'by_type' => $byType,
            'false_by_rule' => $falseByRule,
        ];
    }
}
