<?php

use App\Models\User;

function registration(array $overrides = []): array
{
    return array_merge([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '0771112233',
        'role' => 'grower',
        'password' => 'password',
        'password_confirmation' => 'password',
    ], $overrides);
}

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register as a grower', function () {
    $response = $this->post('/register', registration());

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'test@example.com')->first();
    expect($user->role)->toBe('grower');
    expect($user->phone)->toBe('0771112233');
});

test('a collector can register and gets the collector role', function () {
    $this->post('/register', registration(['role' => 'collector']));

    $this->assertAuthenticated();
    expect(User::where('email', 'test@example.com')->first()->role)->toBe('collector');
});

test('nobody can register as a buyer or invent a role', function () {
    foreach (['buyer', 'admin', ''] as $role) {
        $this->post('/register', registration(['role' => $role]))->assertSessionHasErrors('role');
        $this->assertGuest();
    }
});

test('the phone number is required, well formed and unique', function () {
    $this->post('/register', registration(['phone' => '']))->assertSessionHasErrors('phone');
    $this->post('/register', registration(['phone' => '12345']))->assertSessionHasErrors('phone');
    $this->assertGuest();

    User::factory()->create(['phone' => '0771112233']);
    $this->post('/register', registration(['email' => 'other@example.com']))->assertSessionHasErrors('phone');
    $this->assertGuest();
});

test('spaces in the phone number are removed', function () {
    $this->post('/register', registration(['phone' => '077 111 2233']));

    $this->assertAuthenticated();
    expect(User::where('email', 'test@example.com')->first()->phone)->toBe('0771112233');
});
