<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function data(array $override = []): array
    {
        return array_merge([
            'name' => 'Emma Buyer',
            'email' => 'emma@example.com',
            'password' => 'Str0ng-Pass!2026',
            'password_confirmation' => 'Str0ng-Pass!2026',
        ], $override);
    }

    public function test_anyone_can_register_as_a_buyer_without_role_or_phone(): void
    {
        $this->post('/register', $this->data())->assertRedirect('/');

        $this->assertAuthenticated();

        $user = User::where('email', 'emma@example.com')->first();
        $this->assertSame('buyer', $user->role);
        $this->assertNull($user->phone);
        $this->assertNull($user->brand);
    }

    public function test_an_international_phone_number_is_accepted_and_cleaned(): void
    {
        $this->post('/register', $this->data(['phone' => '+44 7700 900123']))->assertRedirect('/');

        $this->assertSame('+447700900123', User::where('email', 'emma@example.com')->value('phone'));
    }

    public function test_a_sri_lankan_phone_number_is_still_accepted(): void
    {
        $this->post('/register', $this->data(['phone' => '077 111 2233']))->assertRedirect('/');

        $this->assertSame('0771112233', User::where('email', 'emma@example.com')->value('phone'));
    }

    public function test_a_bad_phone_number_is_rejected(): void
    {
        $this->post('/register', $this->data(['phone' => '12345']))
            ->assertSessionHasErrors('phone');

        $this->assertGuest();
    }

    public function test_many_buyers_can_register_without_a_phone(): void
    {
        User::factory()->create(['phone' => null]);

        $this->post('/register', $this->data())->assertRedirect('/');

        $this->assertSame(2, User::whereNull('phone')->count());
    }

    public function test_nobody_can_pick_a_privileged_role(): void
    {
        foreach (['admin', 'seller'] as $role) {
            $this->post('/register', $this->data(['role' => $role]))
                ->assertSessionHasErrors('role');
        }

        $this->assertGuest();
        $this->assertSame(0, User::where('email', 'emma@example.com')->count());
    }
}