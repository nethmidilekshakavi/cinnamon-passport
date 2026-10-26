<?php

namespace App\Console\Commands;

use App\Http\Requests\StoreBatchRequest;
use App\Services\SmsParser;
use App\Services\StudyScenarios;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class RunScenarioTest extends Command
{
    protected $signature = 'cinnamon:scenario-test';

    protected $description = 'RQ1: run the study scenarios through the SMS parser and the app form rules with typical input mistakes';

    private const LABELS = [
        'ok' => 'ok',
        'caught' => 'caught',
        'false_reject' => 'rejected valid',
        'silent_error' => 'silent error',
    ];

    public function handle(SmsParser $parser): int
    {
        $today = CarbonImmutable::today();
        $rules = (new StoreBatchRequest())->rules();

        $results = [];
        $cost = [];

        foreach (range(1, StudyScenarios::COUNT) as $id) {
            $expected = StudyScenarios::expected($id, $today);
            $cases = $this->cases($expected, $today);

            $cost[] = [
                $id,
                mb_strlen($cases[0]['sms']),
                count(array_filter($cases[0]['app'], fn ($v) => $v !== null && $v !== '')),
            ];

            foreach ($cases as $case) {
                if ($case['sms'] !== null) {
                    $parsed = $parser->parse($case['sms'], $today);
                    $results[] = $this->row(
                        $id, $case, 'sms', $parsed['ok'], $parsed['data'],
                        array_column($parsed['errors'], 'code'), $expected
                    );
                }

                if ($case['app'] !== null) {
                    $validator = Validator::make($case['app'], $rules);
                    $results[] = $this->row(
                        $id, $case, 'app', ! $validator->fails(), $case['app'],
                        array_keys($validator->errors()->toArray()), $expected
                    );
                }
            }
        }

        $this->summary($results);
        $this->detail($results);

        $this->newLine();
        $this->line('Interaction cost for a correct entry (proxy, not a time measurement)');
        $this->table(['Scenario', 'SMS characters', 'App fields filled'], $cost);

        $path = $this->saveCsv($results);
        $this->newLine();
        $this->info('Saved '.count($results).' runs to '.$path);

        return self::SUCCESS;
    }

    /** @return array<int, array<string, mixed>> */
    private function cases(array $e, CarbonImmutable $today): array
    {
        $fmt = fn (float $n) => rtrim(rtrim(number_format($n, 1, '.', ''), '0'), '.');
        $kg = $fmt($e['weight_kg']);
        $bigKg = $fmt($e['weight_kg'] * 10);
        $tok = strtoupper(CarbonImmutable::parse($e['harvest_date'])->format('jM'));
        $tomorrow = $today->addDay();

        $sms = fn (array $o = []) => $this->smsText(array_merge([
            'plot' => $e['plot'],
            'weight' => $kg.'KG',
            'date' => $tok,
            'dry' => 'DRY'.$e['drying_days'],
            'grade' => $e['grade'],
        ], $o));

        $app = fn (array $o = []) => array_merge([
            'plot' => $e['plot'],
            'weight_kg' => $kg,
            'harvest_date' => $e['harvest_date'],
            'drying_days' => $e['drying_days'],
            'grade' => $e['grade'],
        ], $o);

        return [
            ['key' => 'clean', 'label' => 'Correct entry', 'same' => true,
                'sms' => $sms(), 'app' => $app()],
            ['key' => 'lowercase', 'label' => 'Typed in lowercase', 'same' => true,
                'sms' => strtolower($sms()), 'app' => $app(['plot' => strtolower($e['plot'])])],
            ['key' => 'spaces', 'label' => 'Extra spaces', 'same' => true,
                'sms' => '  '.str_replace(' ', '   ', $sms()).'  ', 'app' => $app(['plot' => ' '.$e['plot'].' '])],
            ['key' => 'unit_typo', 'label' => 'Weight with unit typo (35K / 35kg)', 'same' => true,
                'sms' => $sms(['weight' => $kg.'K']), 'app' => $app(['weight_kg' => $kg.'kg'])],
            ['key' => 'split_dry', 'label' => 'Space inside "DRY 4"', 'same' => true,
                'sms' => $sms(['dry' => 'DRY '.$e['drying_days']]), 'app' => null],
            ['key' => 'reordered', 'label' => 'Fields in a different order', 'same' => true,
                'sms' => implode(' ', array_filter(['BATCH', $e['plot'], $e['grade'], 'DRY'.$e['drying_days'], $tok, $kg.'KG'])),
                'app' => null],
            ['key' => 'bad_command', 'label' => 'Command word mistyped (BATC)', 'same' => true,
                'sms' => str_replace('BATCH', 'BATC', $sms()), 'app' => null],
            ['key' => 'no_weight', 'label' => 'Weight left out', 'same' => false,
                'sms' => $sms(['weight' => null]), 'app' => $app(['weight_kg' => null])],
            ['key' => 'no_date', 'label' => 'Date left out', 'same' => false,
                'sms' => $sms(['date' => null]), 'app' => $app(['harvest_date' => null])],
            ['key' => 'no_drying', 'label' => 'Drying days left out', 'same' => false,
                'sms' => $sms(['dry' => null]), 'app' => $app(['drying_days' => null])],
            ['key' => 'no_grade', 'label' => 'Grade left out', 'same' => false,
                'sms' => $sms(['grade' => null]), 'app' => $app(['grade' => null])],
            ['key' => 'extra_zero_weight', 'label' => 'Extra zero on weight (350 for 35)', 'same' => false,
                'sms' => $sms(['weight' => $bigKg.'KG']), 'app' => $app(['weight_kg' => $bigKg])],
            ['key' => 'extra_zero_drying', 'label' => 'Extra zero on drying days (40 for 4)', 'same' => false,
                'sms' => $sms(['dry' => 'DRY'.($e['drying_days'] * 10)]), 'app' => $app(['drying_days' => $e['drying_days'] * 10])],
            ['key' => 'impossible_date', 'label' => 'Impossible date (31 Feb)', 'same' => false,
                'sms' => $sms(['date' => '31FEB']), 'app' => $app(['harvest_date' => $today->year.'-02-31'])],
            ['key' => 'future_date', 'label' => 'Tomorrow typed as the date', 'same' => false,
                'sms' => $sms(['date' => strtoupper($tomorrow->format('jM'))]), 'app' => $app(['harvest_date' => $tomorrow->toDateString()])],
            ['key' => 'bad_grade', 'label' => 'Unknown grade (X9)', 'same' => false,
                'sms' => $sms(['grade' => 'X9']), 'app' => $app(['grade' => 'X9'])],
        ];
    }

    private function smsText(array $p): string
    {
        $parts = ['BATCH', $p['plot'], $p['weight'], $p['date'], $p['dry'], $p['grade']];

        return implode(' ', array_filter($parts, fn ($v) => $v !== null && $v !== ''));
    }

    private function row(int $id, array $case, string $channel, bool $accepted, array $data, array $codes, array $expected): array
    {
        if ($accepted) {
            $outcome = $this->same($data, $expected) ? 'ok' : 'silent_error';
        } else {
            $outcome = $case['same'] ? 'false_reject' : 'caught';
        }

        return [
            'scenario' => $id,
            'case' => $case['key'],
            'label' => $case['label'],
            'same' => $case['same'],
            'channel' => $channel,
            'outcome' => $outcome,
            'codes' => implode('|', $codes),
        ];
    }

    private function same(array $got, array $expected): bool
    {
        $plot = preg_replace('/[^A-Z0-9]/', '', strtoupper((string) ($got['plot'] ?? '')));

        return $plot === $expected['plot']
            && abs((float) ($got['weight_kg'] ?? 0) - $expected['weight_kg']) < 0.05
            && ($got['harvest_date'] ?? null) === $expected['harvest_date']
            && (int) ($got['drying_days'] ?? 0) === $expected['drying_days']
            && (($got['grade'] ?? null) ?: null) === $expected['grade'];
    }

    private function summary(array $results): void
    {
        $pct = fn (int $a, int $b) => $b === 0 ? '-' : sprintf('%d/%d (%.1f%%)', $a, $b, 100 * $a / $b);
        $table = [];

        foreach (['sms' => 'SMS', 'app' => 'App form'] as $channel => $name) {
            $rows = array_filter($results, fn ($r) => $r['channel'] === $channel);
            $count = fn (string $o) => count(array_filter($rows, fn ($r) => $r['outcome'] === $o));
            $valid = count(array_filter($rows, fn ($r) => $r['same']));
            $caught = $count('caught');
            $silent = $count('silent_error');

            $table[] = [
                $name,
                count($rows),
                $pct($count('false_reject'), $valid),
                $pct($caught, $caught + $silent),
                $pct($silent, $caught + $silent),
            ];
        }

        $this->table(
            ['Channel', 'Runs', 'Valid entries wrongly rejected', 'Slips caught', 'Slips saved silently'],
            $table
        );
    }

    private function detail(array $results): void
    {
        $tally = function (array $rows): string {
            if ($rows === []) {
                return 'n/a';
            }
            $counts = array_count_values(array_column($rows, 'outcome'));
            $parts = [];
            foreach (self::LABELS as $key => $label) {
                if (isset($counts[$key])) {
                    $parts[] = $label.' '.$counts[$key];
                }
            }

            return implode(', ', $parts);
        };

        $detail = [];
        foreach (array_unique(array_column($results, 'case')) as $case) {
            $mine = array_filter($results, fn ($r) => $r['case'] === $case);
            $first = reset($mine);
            $detail[] = [
                $first['label'],
                $tally(array_filter($mine, fn ($r) => $r['channel'] === 'sms')),
                $tally(array_filter($mine, fn ($r) => $r['channel'] === 'app')),
            ];
        }

        $this->newLine();
        $this->table(['Mistake (4 scenarios each)', 'SMS', 'App form'], $detail);
    }

    private function saveCsv(array $results): string
    {
        $dir = storage_path('app/rq1');
        File::ensureDirectoryExists($dir);
        $path = $dir.'/scenario-results.csv';

        $handle = fopen($path, 'w');
        fputcsv($handle, ['scenario', 'mistake', 'channel', 'outcome', 'system_codes']);
        foreach ($results as $r) {
            fputcsv($handle, [$r['scenario'], $r['case'], $r['channel'], $r['outcome'], $r['codes']]);
        }
        fclose($handle);

        return $path;
    }
}
