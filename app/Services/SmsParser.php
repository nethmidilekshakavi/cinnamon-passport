<?php

namespace App\Services;

use Carbon\CarbonImmutable;

/**
 * Turns a farmer's SMS, e.g. "BATCH P1 40KG 12OCT DRY3 ALBA",
 * into structured batch data, or a list of error codes.
 */
class SmsParser
{
       public const COMMANDS = ['BATCH', 'THOGAYA', 'තොගය', 'தொகுதி'];

    public const GRADES = ['ALBA', 'C5', 'C4', 'H1'];

    private const MONTHS = [
        'JAN' => 1, 'FEB' => 2, 'MAR' => 3, 'APR' => 4, 'MAY' => 5, 'JUN' => 6,
        'JUL' => 7, 'AUG' => 8, 'SEP' => 9, 'OCT' => 10, 'NOV' => 11, 'DEC' => 12,
    ];

    /**
     * @return array{ok: bool, data: array, errors: array<int, array{code: string, value?: ?string}>}
     */
    public function parse(string $text, ?CarbonImmutable $today = null): array
    {
        $today ??= CarbonImmutable::today();
        $errors = [];
        $data = [
            'plot' => null,
            'weight_kg' => null,
            'harvest_date' => null,
            'drying_days' => null,
            'grade' => null,
        ];

        $tokens = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);

        if (! $tokens || ! in_array(mb_strtoupper($tokens[0]), self::COMMANDS, true)) {
            return ['ok' => false, 'data' => $data, 'errors' => [['code' => 'command']]];
        }

        array_shift($tokens);

        $plot = array_shift($tokens);
        if ($plot === null || ! preg_match('/^[A-Za-z0-9-]{1,20}$/', $plot)) {
            $errors[] = ['code' => 'plot', 'value' => $plot];
        } else {
            $data['plot'] = strtoupper($plot);
        }

        $tokens = $this->joinSplitDrying($tokens);

        $dateFailed = false;

        foreach ($tokens as $token) {
            $up = strtoupper($token);

            if (preg_match('/^(\d+(?:\.\d+)?)KG$/', $up, $m)) {
                $data['weight_kg'] = (float) $m[1];
            } elseif (preg_match('/^DRY(\d{1,2})$/', $up, $m)) {
                $data['drying_days'] = (int) $m[1];
            } elseif (preg_match('/^(\d{1,2})([A-Z]{3})$/', $up, $m) && isset(self::MONTHS[$m[2]])) {
                $date = $this->resolveDate((int) $m[1], self::MONTHS[$m[2]], $today);
                if ($date === null) {
                    $dateFailed = true;
                    $errors[] = ['code' => 'date', 'value' => $token];
                } else {
                    $data['harvest_date'] = $date->toDateString();
                }
            } elseif (in_array($up, self::GRADES, true)) {
                $data['grade'] = $up;
            } else {
                $errors[] = ['code' => 'unknown', 'value' => $token];
            }
        }

        if ($data['weight_kg'] === null) {
            $errors[] = ['code' => 'weight'];
        } elseif ($data['weight_kg'] < 0.1 || $data['weight_kg'] > 500) {
            $errors[] = ['code' => 'weight_range'];
        }

        if ($data['harvest_date'] === null && ! $dateFailed) {
            $errors[] = ['code' => 'date'];
        }

        if ($data['drying_days'] === null) {
            $errors[] = ['code' => 'drying'];
        } elseif ($data['drying_days'] < 1 || $data['drying_days'] > 30) {
            $errors[] = ['code' => 'drying_range'];
        }

        return ['ok' => count($errors) === 0, 'data' => $data, 'errors' => $errors];
    }

    /** "DRY 4" typed with a space counts as "DRY4". */
    private function joinSplitDrying(array $tokens): array
    {
        $out = [];

        for ($i = 0; $i < count($tokens); $i++) {
            if (strtoupper($tokens[$i]) === 'DRY' && isset($tokens[$i + 1]) && preg_match('/^\d{1,2}$/', $tokens[$i + 1])) {
                $out[] = 'DRY'.$tokens[$i + 1];
                $i++;

                continue;
            }

            $out[] = $tokens[$i];
        }

        return $out;
    }

    /**
     * Year is not typed in an SMS: use this year. A date a few days ahead is a
     * typing mistake, so it is rejected; a date further ahead is last year's.
     */
    private function resolveDate(int $day, int $month, CarbonImmutable $today): ?CarbonImmutable
    {
        $year = $today->year;

        if (! checkdate($month, $day, $year)) {
            return null;
        }

        $date = CarbonImmutable::create($year, $month, $day)->startOfDay();

        if ($date->greaterThan($today)) {
            // A date a few days ahead is a typing mistake, not last year's harvest.
            if ($date->lessThanOrEqualTo($today->addDays(31))) {
                return null;
            }

            $year--;
            if (! checkdate($month, $day, $year)) {
                return null;
            }
            $date = CarbonImmutable::create($year, $month, $day)->startOfDay();
        }

        return $date;
    }
}
