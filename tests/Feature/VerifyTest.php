<?php

use App\Models\Batch;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('shows a batch passport to anyone with the code', function () {
    $batch = Batch::factory()->create();

    $this->get('/verify/'.$batch->code)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Verify/Show')
            ->where('batch.code', $batch->code));
});

it('does not expose grower details or notes', function () {
    $batch = Batch::factory()->create();

    $this->get('/verify/'.$batch->code)
        ->assertInertia(fn (Assert $page) => $page
            ->missing('batch.grower_id')
            ->missing('batch.notes'));
});

it('returns 404 for an unknown code', function () {
    $this->get('/verify/CIN-9999')->assertNotFound();
});

it('looks a code up case-insensitively', function () {
    $batch = Batch::factory()->create();

    $this->post('/verify', ['code' => strtolower($batch->code)])
        ->assertRedirect('/verify/'.$batch->code);
});

it('reports an unknown code on lookup', function () {
    $this->post('/verify', ['code' => 'NOPE-1'])->assertSessionHasErrors('code');
});

it('gives the buyer the verification page as home', function () {
    $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

    $this->actingAs($buyer)->get('/buyer')->assertOk();
});
