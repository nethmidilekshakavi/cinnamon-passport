<?php

use App\Services\SmsParser;
use Carbon\CarbonImmutable;

it('accepts the Singlish command word THOGAYA', function () {
    $today = CarbonImmutable::parse('2026-10-26');

    $result = (new SmsParser())->parse('thogaya P1 40KG 12OCT DRY3', $today);

    expect($result['ok'])->toBeTrue()
        ->and($result['data']['plot'])->toBe('P1');
});
