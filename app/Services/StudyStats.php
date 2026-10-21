<?php

namespace App\Services;

use App\Models\StudySus;
use App\Models\StudyTrial;

class StudyStats
{
    /** Standard SUS: odd items (answer-1), even items (5-answer), sum x 2.5 -> 0..100. */
    public static function susScore(array $answers): float
    {
        $sum = 0;

        foreach (array_values($answers) as $i => $answer) {
            $sum += $i % 2 === 0 ? ((int) $answer - 1) : (5 - (int) $answer);
        }

        return $sum * 2.5;
    }

    /** Per-channel summary for the results page. */
    public function summary(): array
    {
        $out = [];

        foreach (['sms', 'app'] as $channel) {
            $trials = StudyTrial::where('channel', $channel)->whereNotNull('finished_at')->get();
            $ok = $trials->where('success', true);
            $sus = StudySus::where('channel', $channel)->pluck('score');
            $n = $trials->count();

            $out[$channel] = [
                'trials' => $n,
                'success' => $ok->count(),
                'success_rate' => $n ? round($ok->count() / $n * 100, 1) : null,
                'median_seconds' => self::median($ok->pluck('duration_ms')->map(fn ($ms) => $ms / 1000)->all()),
                'mean_attempts' => self::rounded($trials->avg('attempts')),
                'mean_errors' => self::rounded($trials->avg('errors')),
                'mean_data_errors' => self::rounded($ok->avg('data_errors')),
                'sus_mean' => self::rounded($sus->avg()),
                'sus_n' => $sus->count(),
            ];
        }

        return $out;
    }

    private static function median(array $values): ?float
    {
        if ($values === []) {
            return null;
        }

        sort($values);
        $n = count($values);
        $mid = intdiv($n, 2);

        return round($n % 2 ? $values[$mid] : ($values[$mid - 1] + $values[$mid]) / 2, 1);
    }

    private static function rounded(float|int|null $value): ?float
    {
        return $value === null ? null : round($value, 2);
    }
}
