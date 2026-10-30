<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function growerSends(string $message)
{
    $user = User::factory()->create(['role' => User::ROLE_GROWER]);

    return [$user, test()->actingAs($user)->post('/sms', ['message' => $message])];
}

it('answers HELP with the message format', function () {
    [, $response] = growerSends('HELP');

    $response->assertSessionHas('sms.reply', fn ($reply) => str_contains($reply, 'Hand over: HAND'));
});

it('answers UDAW in Singlish', function () {
    [, $response] = growerSends('UDAW');

    $response->assertSessionHas('sms.reply', fn ($reply) => str_contains($reply, 'Yawanna'));
});

it('shows a template when only the command word is sent', function () {
    [$user, $response] = growerSends('BATCH');

    $response->assertSessionHas('sms.reply', fn ($reply) => str_contains($reply, 'Send like this'));
    expect($user->batches()->count())->toBe(0);
});

it('saves a batch typed with the Singlish command word and replies in Singlish', function () {
    $date = strtoupper(now()->subDays(5)->format('jM'));
    [$user, $response] = growerSends("THOGAYA P1 40KG {$date} DRY3");

    expect($user->batches()->count())->toBe(1);
    $response->assertSessionHas('sms.reply', fn ($reply) => str_contains($reply, 'Surakuwa'));
});

it('adds a correct example to an error reply', function () {
    [, $response] = growerSends('BATCH P1 40KG');

    $response->assertSessionHas('sms.reply', fn ($reply) => str_contains($reply, 'Example: BATCH P1 40KG 12OCT DRY3 ALBA'));
});
