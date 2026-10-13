<?php

use App\Models\Batch;
use App\Models\User;

it('creates a batch from a valid SMS', function () {
    $user = User::factory()->create(['role' => User::ROLE_GROWER]);

    $this->actingAs($user)
        ->post('/sms', ['message' => 'BATCH P1 40KG 12OCT DRY3 ALBA'])
        ->assertRedirect('/sms')
        ->assertSessionHas('sms.ok', true);

    $batch = $user->batches()->first();

    expect($batch)->not->toBeNull()
        ->and($batch->source)->toBe(Batch::SOURCE_SMS)
        ->and($batch->grade)->toBe('ALBA')
        ->and((float) $batch->weight_kg)->toBe(40.0);
});

it('does not create a batch from a bad SMS', function () {
    $user = User::factory()->create(['role' => User::ROLE_GROWER]);

    $this->actingAs($user)
        ->post('/sms', ['message' => 'HELLO'])
        ->assertSessionHas('sms.ok', false);

    expect($user->batches()->count())->toBe(0);
});

it('replies in Sinhala when the command is Sinhala', function () {
    $user = User::factory()->create(['role' => User::ROLE_GROWER]);

    $this->actingAs($user)
        ->post('/sms', ['message' => 'තොගය P2 30KG 11OCT DRY2 C5'])
        ->assertSessionHas('sms.reply', fn ($reply) => str_contains($reply, 'සුරැකිණි'));
});

it('blocks non-growers from the simulator', function () {
    $collector = User::factory()->create(['role' => User::ROLE_COLLECTOR]);

    $this->actingAs($collector)->get('/sms')->assertForbidden();
});
