<?php

use App\Services\SmsParser;
use Carbon\CarbonImmutable;

it('rejects a harvest date a few days in the future', function () {
    $today = CarbonImmutable::parse('2026-10-26');

    $result = (new SmsParser())->parse('BATCH P1 40KG 28OCT DRY3', $today);

    expect($result['ok'])->toBeFalse()
        ->and(array_column($result['errors'], 'code'))->toContain('date')
        ->and($result['data']['harvest_date'])->toBeNull();
});

it('still reads a date far ahead as last year', function () {
    $today = CarbonImmutable::parse('2026-01-05');

    $result = (new SmsParser())->parse('BATCH P1 40KG 28DEC DRY3', $today);

    expect($result['ok'])->toBeTrue()
        ->and($result['data']['harvest_date'])->toBe('2025-12-28');
});

it('accepts 500 kg and rejects more, like the app form', function () {
    $today = CarbonImmutable::parse('2026-10-26');
    $parser = new SmsParser();

    expect($parser->parse('BATCH P1 500KG 20OCT DRY3', $today)['ok'])->toBeTrue();

    $heavy = $parser->parse('BATCH P1 501KG 20OCT DRY3', $today);

    expect($heavy['ok'])->toBeFalse()
        ->and(array_column($heavy['errors'], 'code'))->toContain('weight_range');
});

it('accepts DRY with a space before the number', function () {
    $today = CarbonImmutable::parse('2026-10-26');

    $result = (new SmsParser())->parse('BATCH P1 40KG 12OCT DRY 3 ALBA', $today);

    expect($result['ok'])->toBeTrue()
        ->and($result['data']['drying_days'])->toBe(3);
});

it('uses the same weight and drying limits as the app form', function () {
    $today = CarbonImmutable::parse('2026-10-26');
    $parser = new SmsParser();

    $light = $parser->parse('BATCH P1 0.05KG 20OCT DRY3', $today);
    $zero = $parser->parse('BATCH P1 40KG 20OCT DRY0', $today);

    expect(array_column($light['errors'], 'code'))->toContain('weight_range')
        ->and(array_column($zero['errors'], 'code'))->toContain('drying_range');
});
