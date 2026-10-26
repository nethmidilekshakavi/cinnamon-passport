<?php

use App\Services\SmsParser;
use Carbon\CarbonImmutable;

function smsParse(string $text): array
{
    return (new SmsParser())->parse($text, CarbonImmutable::create(2026, 10, 13));
}

function smsCodes(array $result): array
{
    return array_column($result['errors'], 'code');
}

it('parses a full English SMS', function () {
    $r = smsParse('BATCH P1 40KG 12OCT DRY3 ALBA');

    expect($r['ok'])->toBeTrue()
        ->and($r['data'])->toBe([
            'plot' => 'P1',
            'weight_kg' => 40.0,
            'harvest_date' => '2026-10-12',
            'drying_days' => 3,
            'grade' => 'ALBA',
        ]);
});

it('is case-insensitive and grade is optional', function () {
    $r = smsParse('batch p1 25kg 10oct dry4');

    expect($r['ok'])->toBeTrue()
        ->and($r['data']['plot'])->toBe('P1')
        ->and($r['data']['grade'])->toBeNull();
});

it('accepts Sinhala and Tamil commands', function () {
    expect(smsParse('තොගය P2 30KG 11OCT DRY2 C5')['ok'])->toBeTrue();
    expect(smsParse('தொகுதி P3 30KG 11OCT DRY2')['ok'])->toBeTrue();
});

it('uses last year when the date would be in the future', function () {
    expect(smsParse('BATCH P1 10KG 20DEC DRY3')['data']['harvest_date'])->toBe('2025-12-20');
});

it('rejects an unknown command', function () {
    expect(smsCodes(smsParse('HELLO P1 40KG')))->toBe(['command']);
});

it('reports a missing weight', function () {
    expect(smsCodes(smsParse('BATCH P1 12OCT DRY3')))->toBe(['weight']);
});

it('reports an impossible date', function () {
    expect(smsCodes(smsParse('BATCH P1 40KG 31FEB DRY3')))->toBe(['date']);
});

it('reports out-of-range drying days', function () {
    expect(smsCodes(smsParse('BATCH P1 40KG 12OCT DRY99')))->toBe(['drying_range']);
});

it('reports unknown tokens', function () {
    expect(smsCodes(smsParse('BATCH P1 40KG 12OCT DRY3 XYZ')))->toBe(['unknown']);
});
