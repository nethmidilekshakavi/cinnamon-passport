<?php

use App\Models\Batch;
use App\Models\User;

function handoverSetup(): array
{
    $grower = User::factory()->create(['role' => User::ROLE_GROWER]);
    $collector = User::factory()->create(['role' => User::ROLE_COLLECTOR, 'phone' => '0771234567']);
    $batch = Batch::factory()->for($grower, 'grower')->create();

    return [$grower, $collector, $batch];
}

it('lets a grower request a handover by SMS', function () {
    [$grower, $collector, $batch] = handoverSetup();

    $this->actingAs($grower)
        ->post('/sms', ['message' => "HAND {$batch->code} 0771234567"])
        ->assertSessionHas('sms.ok', true);

    $batch->refresh();

    expect((int) $batch->collector_id)->toBe($collector->id)
        ->and($batch->handover_requested_at)->not->toBeNull()
        ->and($batch->handed_over_at)->toBeNull();
});

it('lets the named collector confirm by SMS', function () {
    [$grower, $collector, $batch] = handoverSetup();
    $batch->forceFill(['collector_id' => $collector->id, 'handover_requested_at' => now()])->save();

    $this->actingAs($collector)
        ->post('/sms', ['message' => "OK {$batch->code}"])
        ->assertSessionHas('sms.ok', true);

    $batch->refresh();

    expect($batch->handed_over_at)->not->toBeNull()
        ->and($batch->status)->toBe(Batch::STATUS_HANDED_OVER);
});

it('does not let a different collector confirm', function () {
    [$grower, $collector, $batch] = handoverSetup();
    $batch->forceFill(['collector_id' => $collector->id, 'handover_requested_at' => now()])->save();

    $other = User::factory()->create(['role' => User::ROLE_COLLECTOR, 'phone' => '0779999999']);

    $this->actingAs($other)
        ->post('/sms', ['message' => "OK {$batch->code}"])
        ->assertSessionHas('sms.ok', false);

    expect($batch->refresh()->handed_over_at)->toBeNull();
});

it('does not let a grower hand over someone elses batch', function () {
    [$grower, $collector, $batch] = handoverSetup();
    $stranger = User::factory()->create(['role' => User::ROLE_GROWER]);

    $this->actingAs($stranger)
        ->post('/sms', ['message' => "HAND {$batch->code} 0771234567"])
        ->assertSessionHas('sms.ok', false);

    expect($batch->refresh()->collector_id)->toBeNull();
});

it('does not let a grower confirm a handover', function () {
    [$grower, $collector, $batch] = handoverSetup();
    $batch->forceFill(['collector_id' => $collector->id, 'handover_requested_at' => now()])->save();

    $this->actingAs($grower)
        ->post('/sms', ['message' => "OK {$batch->code}"])
        ->assertSessionHas('sms.ok', false);

    expect($batch->refresh()->handed_over_at)->toBeNull();
});

it('replies in Sinhala to a Sinhala handover command', function () {
    [$grower, $collector, $batch] = handoverSetup();

    $this->actingAs($grower)
        ->post('/sms', ['message' => "භාර {$batch->code} 0771234567"])
        ->assertSessionHas('sms.reply', fn ($reply) => preg_match('/\p{Sinhala}/u', $reply) === 1);
});

it('supports the same handover through the app', function () {
    [$grower, $collector, $batch] = handoverSetup();

    $this->actingAs($grower)
        ->post("/batches/{$batch->id}/handover", ['collector_id' => $collector->id])
        ->assertRedirect();

    expect($batch->refresh()->handover_requested_at)->not->toBeNull();

    $this->actingAs($collector)
        ->post("/collector/handovers/{$batch->id}/confirm")
        ->assertRedirect();

    expect($batch->refresh()->handed_over_at)->not->toBeNull();
});

it('shows the collector desk to collectors only', function () {
    [$grower, $collector] = handoverSetup();

    $this->actingAs($collector)->get('/collector')->assertOk();
    $this->actingAs($grower)->get('/collector')->assertForbidden();
});
