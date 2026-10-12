<?php

use App\Models\Batch;
use App\Models\User;

it('gives each batch a readable code after saving', function () {
    $batch = Batch::factory()->create();

    expect($batch->fresh()->code)->toBe(sprintf('CIN-%04d', $batch->id));
});

it('belongs to a grower', function () {
    $batch = Batch::factory()->create();

    expect($batch->grower)->toBeInstanceOf(User::class);
});

it('is listed on its grower', function () {
    $grower = User::factory()->create();
    Batch::factory()->count(2)->create(['grower_id' => $grower->id]);

    expect($grower->batches)->toHaveCount(2);
});
