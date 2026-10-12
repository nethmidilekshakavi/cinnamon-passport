<?php

use App\Models\Batch;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('lets a grower record a batch', function () {
    $grower = User::factory()->create(['role' => 'grower']);

    $this->actingAs($grower)->post('/batches', [
        'plot' => 'P1',
        'weight_kg' => '40',
        'harvest_date' => now()->toDateString(),
        'drying_days' => 3,
        'grade' => 'ALBA',
    ])->assertRedirect('/batches');

    expect($grower->batches()->count())->toBe(1);
    expect($grower->batches()->first()->source)->toBe('app');
});

it('rejects a batch with no weight', function () {
    $grower = User::factory()->create(['role' => 'grower']);

    $this->actingAs($grower)->post('/batches', [
        'plot' => 'P1',
        'harvest_date' => now()->toDateString(),
        'drying_days' => 3,
    ])->assertSessionHasErrors('weight_kg');
});

it('blocks a collector from recording batches', function () {
    $collector = User::factory()->create(['role' => 'collector']);

    $this->actingAs($collector)->post('/batches', [])->assertForbidden();
});

it('shows a grower only their own batches', function () {
    $mine = User::factory()->create(['role' => 'grower']);
    $other = User::factory()->create(['role' => 'grower']);
    Batch::factory()->create(['grower_id' => $mine->id]);
    Batch::factory()->create(['grower_id' => $other->id]);

    $this->actingAs($mine)->get('/batches')->assertInertia(
        fn (Assert $page) => $page->component('Batches/Index')->has('batches', 1)
    );
});
