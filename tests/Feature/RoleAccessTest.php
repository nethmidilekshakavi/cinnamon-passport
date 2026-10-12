<?php

use App\Models\User;

it('lets a collector open the collector area', function () {
    $user = User::factory()->create(['role' => 'collector']);

    $this->actingAs($user)->get('/collector')->assertOk();
});

it('blocks a grower from the collector area', function () {
    $user = User::factory()->create(['role' => 'grower']);

    $this->actingAs($user)->get('/collector')->assertForbidden();
});

it('sends guests to the login page', function () {
    $this->get('/buyer')->assertRedirect('/login');
});
