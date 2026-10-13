<?php

namespace App\Services;

use App\Models\Batch;

/** Builds the short reply SMS in the language the farmer wrote in. */
class SmsReplies
{
    private const TEXT = [
        'en' => [
            'ok' => 'OK {code} saved: {plot}, {kg}kg, {date}, dried {days} days.',
            'fail' => 'ERROR:',
            'err' => [
                'command' => 'Start with BATCH. Example: BATCH P1 40KG 12OCT DRY3 ALBA',
                'plot' => 'Plot name missing (e.g. P1)',
                'weight' => 'Weight missing (e.g. 40KG)',
                'weight_range' => 'Weight must be 1-5000 kg',
                'date' => 'Date missing or wrong (e.g. 12OCT)',
                'drying' => 'Drying days missing (e.g. DRY3)',
                'drying_range' => 'Drying days must be 0-30',
                'unknown' => 'Not understood: {value}',
            ],
        ],
        'si' => [
            'ok' => 'සුරැකිණි {code}: {plot}, {kg}kg, {date}, වේලූ දින {days}.',
            'fail' => 'වැරදියි:',
            'err' => [
                'command' => 'තොගය ලෙස පටන් ගන්න. උදා: තොගය P1 40KG 12OCT DRY3 ALBA',
                'plot' => 'ඉඩම් කොටස නැත (උදා: P1)',
                'weight' => 'බර නැත (උදා: 40KG)',
                'weight_range' => 'බර කි.ග්‍රෑ. 1-5000 අතර විය යුතුයි',
                'date' => 'දිනය නැත/වැරදියි (උදා: 12OCT)',
                'drying' => 'වේලූ දින නැත (උදා: DRY3)',
                'drying_range' => 'වේලූ දින 0-30 අතර විය යුතුයි',
                'unknown' => 'තේරුණේ නැත: {value}',
            ],
        ],
        'ta' => [
            'ok' => 'பதிவானது {code}: {plot}, {kg}kg, {date}, உலர்த்தல் {days} நாள்.',
            'fail' => 'பிழை:',
            'err' => [
                'command' => 'தொகுதி என்று தொடங்கவும். எ.கா: தொகுதி P1 40KG 12OCT DRY3 ALBA',
                'plot' => 'நிலப் பகுதி இல்லை (எ.கா: P1)',
                'weight' => 'எடை இல்லை (எ.கா: 40KG)',
                'weight_range' => 'எடை 1-5000 கிலோ இருக்க வேண்டும்',
                'date' => 'தேதி இல்லை/தவறு (எ.கா: 12OCT)',
                'drying' => 'உலர்த்தும் நாட்கள் இல்லை (எ.கா: DRY3)',
                'drying_range' => 'உலர்த்தும் நாட்கள் 0-30 இருக்க வேண்டும்',
                'unknown' => 'புரியவில்லை: {value}',
            ],
        ],
    ];

    public static function languageOf(string $text): string
    {
        $parts = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $first = mb_strtoupper($parts[0] ?? '');

        return match ($first) {
            'තොගය' => 'si',
            'தொகுதி' => 'ta',
            default => 'en',
        };
    }

    public static function success(string $lang, Batch $batch): string
    {
        return strtr(self::TEXT[$lang]['ok'], [
            '{code}' => $batch->code,
            '{plot}' => $batch->plot,
            '{kg}' => (string) (float) $batch->weight_kg,
            '{date}' => substr((string) $batch->harvest_date, 0, 10),
            '{days}' => (string) $batch->drying_days,
        ]);
    }

    public static function failure(string $lang, array $errors): string
    {
        $messages = [];

        foreach ($errors as $error) {
            $template = self::TEXT[$lang]['err'][$error['code']] ?? self::TEXT[$lang]['err']['unknown'];
            $messages[] = strtr($template, ['{value}' => $error['value'] ?? '']);
        }

        return self::TEXT[$lang]['fail'].' '.implode('; ', array_unique($messages));
    }
}
