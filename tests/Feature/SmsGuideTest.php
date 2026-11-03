<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the SMS guide to a grower', function () {
    $user = User::factory()->create(['role' => User::ROLE_GROWER]);

    $this->actingAs($user)->get('/sms/guide')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Sms/Guide'));
});

it('blocks buyers from the SMS guide', function () {
    $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

    $this->actingAs($buyer)->get('/sms/guide')->assertForbidden();
});

it('sends guests to the login page from the SMS guide', function () {
    $this->get('/sms/guide')->assertRedirect('/login');
});
