<?php

namespace App\Services;

use App\Models\Batch;

/** Builds the short reply SMS in the language the user wrote in. */
class SmsReplies
{
    private const FLAG_NOTE = [
        'en' => 'Flagged for review.',
        'si' => 'සමාලෝචනයට සලකුණු කළා.',
        'ta' => 'மதிப்பாய்வுக்காகக் குறிக்கப்பட்டது.',
    ];

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

    private const HANDOVER = [
        'en' => [
            'requested' => 'OK {code}: handover to {collector} requested. They must confirm.',
            'confirmed' => 'OK {code} received from {grower}. Grower notified.',
            'no_batch' => 'Batch {code} not found in your list.',
            'already' => '{code} is already handed over.',
            'no_collector' => 'No collector found with that phone number.',
            'nothing_pending' => 'No pending handover for {code}.',
            'role' => 'This command is not available for your account.',
            'format_hand' => 'Format: HAND CIN-0001 0771234567',
            'format_ok' => 'Format: OK CIN-0001',
        ],
        'si' => [
            'requested' => 'සුරැකිණි {code}: {collector} වෙත භාර දීම ඉල්ලා ඇත. ඔවුන් තහවුරු කළ යුතුයි.',
            'confirmed' => '{code} {grower} වෙතින් ලැබුණා. ගොවියාට දැනුම් දුන්නා.',
            'no_batch' => '{code} තොගය ඔබේ ලැයිස්තුවේ නැහැ.',
            'already' => '{code} දැනටමත් භාර දී ඇත.',
            'no_collector' => 'එම දුරකථන අංකයට අදාළ එකතුකරන්නෙකු නැහැ.',
            'nothing_pending' => '{code} සඳහා භාර දීමක් අපේක්ෂිත නැහැ.',
            'role' => 'මෙම විධානය ඔබේ ගිණුමට අදාළ නැහැ.',
            'format_hand' => 'ආකෘතිය: භාර CIN-0001 0771234567',
            'format_ok' => 'ආකෘතිය: තහවුරු CIN-0001',
        ],
        'ta' => [
            'requested' => 'பதிவானது {code}: {collector} இடம் ஒப்படைக்கக் கோரப்பட்டது. அவர் உறுதிசெய்ய வேண்டும்.',
            'confirmed' => '{code} {grower} இடமிருந்து பெறப்பட்டது. விவசாயிக்கு தெரிவிக்கப்பட்டது.',
            'no_batch' => '{code} தொகுதி உங்கள் பட்டியலில் இல்லை.',
            'already' => '{code} ஏற்கனவே ஒப்படைக்கப்பட்டது.',
            'no_collector' => 'அந்த தொலைபேசி எண்ணுக்கு சேகரிப்பாளர் இல்லை.',
            'nothing_pending' => '{code} க்கு நிலுவையில் ஒப்படைப்பு இல்லை.',
            'role' => 'இந்தக் கட்டளை உங்கள் கணக்கிற்கு இல்லை.',
            'format_hand' => 'வடிவம்: ஒப்படை CIN-0001 0771234567',
            'format_ok' => 'வடிவம்: உறுதி CIN-0001',
        ],
    ];

    public static function languageOf(string $text): string
    {
        $parts = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $first = mb_strtoupper($parts[0] ?? '');

        return match ($first) {
            'තොගය', 'භාර', 'තහවුරු' => 'si',
            'தொகுதி', 'ஒப்படை', 'உறுதி' => 'ta',
            default => 'en',
        };
    }

    public static function success(string $lang, Batch $batch): string
    {
        $text = strtr(self::TEXT[$lang]['ok'], [
            '{code}' => $batch->code,
            '{plot}' => $batch->plot,
            '{kg}' => (string) (float) $batch->weight_kg,
            '{date}' => substr((string) $batch->harvest_date, 0, 10),
            '{days}' => (string) $batch->drying_days,
        ]);

        return empty($batch->flags) ? $text : $text.' '.self::FLAG_NOTE[$lang];
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

    public static function handover(string $lang, string $key, array $vars = []): string
    {
        $pairs = [];

        foreach ($vars as $name => $value) {
            $pairs['{'.$name.'}'] = (string) $value;
        }

        return strtr(self::HANDOVER[$lang][$key], $pairs);
    }
}
