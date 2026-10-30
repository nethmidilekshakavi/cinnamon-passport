<?php

namespace App\Services;

/**
 * Help and template replies for growers on basic phones.
 * Languages: en, sg (Singlish, Latin letters only), si, ta.
 */
class SmsHelp
{
    /** Word typed => language of the reply. */
    public const HELP_WORDS = [
        'HELP' => 'en',
        'UDAW' => 'sg',
        'උදව්' => 'si',
        'உதவி' => 'ta',
    ];

    private const COMMAND_LANG = [
        'BATCH' => 'en',
        'THOGAYA' => 'sg',
        'තොගය' => 'si',
        'தொகுதி' => 'ta',
    ];

    private const HELP = [
        'en' => 'Send: BATCH P1 40KG 12OCT DRY3 ALBA (plot, kg, date, drying days, grade is optional). Hand over: HAND CIN-0001 0771234567. Collector: OK CIN-0001',
        'sg' => 'Yawanna: BATCH P1 40KG 12OCT DRY3 ALBA (kaballa, kilo, dinaya, wiyalu dina, grade nethnam bayi). Bhara denna: BHARA CIN-0001 0771234567. Collector: THAHAWURU CIN-0001',
        'si' => 'යවන්න: BATCH P1 40KG 12OCT DRY3 ALBA (කැබැල්ල, කිලෝ, දිනය, වියළු දින, grade අවශ්‍ය නැහැ). භාර දෙන්න: භාර CIN-0001 0771234567. Collector: තහවුරු CIN-0001',
        'ta' => 'அனுப்புங்கள்: BATCH P1 40KG 12OCT DRY3 ALBA (பகுதி, கிலோ, தேதி, உலர்த்திய நாட்கள், தரம் விருப்பம்). ஒப்படைக்க: ஒப்படை CIN-0001 0771234567. Collector: உறுதி CIN-0001',
    ];

    private const TEMPLATE = [
        'en' => 'Send like this: BATCH P1 40KG 12OCT DRY3 ALBA. Use your own plot instead of P1. Send HELP for more.',
        'sg' => 'Mehema yawanna: BATCH P1 40KG 12OCT DRY3 ALBA. P1 wenuwata obe kaballa. Wadi wisthara ta UDAW yawanna.',
        'si' => 'මෙහෙම යවන්න: BATCH P1 40KG 12OCT DRY3 ALBA. P1 වෙනුවට ඔබේ කැබැල්ල දාන්න. වැඩි විස්තරයට උදව් යවන්න.',
        'ta' => 'இப்படி அனுப்புங்கள்: BATCH P1 40KG 12OCT DRY3 ALBA. P1 இடத்தில் உங்கள் பகுதி. மேலும் உதவிக்கு உதவி அனுப்புங்கள்.',
    ];

    public static function languageOf(string $commandWord): string
    {
        return self::COMMAND_LANG[mb_strtoupper($commandWord)] ?? 'en';
    }

    public static function help(string $lang): string
    {
        return self::HELP[$lang] ?? self::HELP['en'];
    }

    public static function template(string $lang): string
    {
        return self::TEMPLATE[$lang] ?? self::TEMPLATE['en'];
    }
}
