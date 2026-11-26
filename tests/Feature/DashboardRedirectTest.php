<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_buyer_goes_to_the_marketplace(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'buyer']))
            ->get('/dashboard')
            ->assertRedirect(route('market.index'));
    }

    public function test_a_seller_goes_to_the_seller_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'buyer']);
        Brand::create(['user_id' => $user->id, 'name' => 'Test Brand', 'district' => 'Galle']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('seller.dashboard'));
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}