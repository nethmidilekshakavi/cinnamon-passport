<?php

namespace App\Services;

use App\Models\Batch;
use Carbon\CarbonInterface;

/** Fixed task scenarios for the usability test, with the values a correct entry must have. */
class StudyScenarios
{
    public const COUNT = 4;

    private const LIST = [
        1 => ['plot' => 'P2', 'weight_kg' => 35.0, 'days_ago' => 2, 'drying_days' => 4, 'grade' => 'C5'],
        2 => ['plot' => 'P1', 'weight_kg' => 52.0, 'days_ago' => 5, 'drying_days' => 3, 'grade' => 'ALBA'],
        3 => ['plot' => 'P4', 'weight_kg' => 28.5, 'days_ago' => 1, 'drying_days' => 5, 'grade' => null],
        4 => ['plot' => 'P3', 'weight_kg' => 60.0, 'days_ago' => 3, 'drying_days' => 6, 'grade' => 'H1'],
    ];

    /** A participant's 1st trial gets scenario 1, the 2nd scenario 2, and so on. */
    public static function idFor(int $index): int
    {
        return ($index % self::COUNT) + 1;
    }

    public static function expected(int $id, CarbonInterface $today): array
    {
        $s = self::LIST[$id];

        return [
            'plot' => $s['plot'],
            'weight_kg' => $s['weight_kg'],
            'harvest_date' => $today->copy()->subDays($s['days_ago'])->toDateString(),
            'drying_days' => $s['drying_days'],
            'grade' => $s['grade'],
        ];
    }

    /** The task card, in all three languages. */
    public static function text(int $id, CarbonInterface $today): array
    {
        $s = self::LIST[$id];
        $date = $today->copy()->subDays($s['days_ago'])->format('j F');
        $kg = rtrim(rtrim(number_format($s['weight_kg'], 1, '.', ''), '0'), '.');
        $grade = $s['grade'];

        return [
            'en' => "You harvested {$kg} kg of cinnamon from plot {$s['plot']} on {$date}. It dried for {$s['drying_days']} days. Grade: "
                .($grade ?? 'not graded yet').'. Record this batch.',
            'si' => "ඔබ {$date} දින {$s['plot']} ඉඩම් කොටසින් කුරුඳු කි.ග්‍රෑ. {$kg} ක් අස්වැන්න ලබා ගත්තා. වේලීමට දින {$s['drying_days']} ක් ගියා. ශ්‍රේණිය: "
                .($grade ?? 'තවම ශ්‍රේණිගත කර නැහැ').'. මෙම තොගය සටහන් කරන්න.',
            'ta' => "நீங்கள் {$date} அன்று {$s['plot']} நிலப் பகுதியிலிருந்து {$kg} கிலோ இலவங்கப்பட்டை அறுவடை செய்தீர்கள். உலர்த்த {$s['drying_days']} நாட்கள் ஆனது. தரம்: "
                .($grade ?? 'இன்னும் தரப்படுத்தவில்லை').'. இந்தத் தொகுதியைப் பதிவு செய்யவும்.',
        ];
    }

    /** How many of the 5 fields the participant saved wrongly (0 = perfect). */
    public static function dataErrors(int $id, CarbonInterface $today, Batch $batch): int
    {
        $expected = self::expected($id, $today);
        $errors = 0;

        // "P2", "p2" and "Plot P2" all count as the same plot.
        $plot = preg_replace('/[^A-Z0-9]/', '', str_replace('PLOT', '', strtoupper($batch->plot)));

        if ($plot !== $expected['plot']) {
            $errors++;
        }
        if (abs((float) $batch->weight_kg - $expected['weight_kg']) > 0.05) {
            $errors++;
        }
        if ($batch->harvest_date->toDateString() !== $expected['harvest_date']) {
            $errors++;
        }
        if ((int) $batch->drying_days !== $expected['drying_days']) {
            $errors++;
        }
        if (($batch->grade ?: null) !== $expected['grade']) {
            $errors++;
        }

        return $errors;
    }
}
