<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StartFlowTest extends TestCase
{
    use RefreshDatabase;

    private function details(): array
    {
        return [
            'name' => 'New Person',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
    }

    public function test_guests_see_the_welcome_page(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_the_start_links_remember_the_choice_and_open_register(): void
    {
        $this->get('/start/seller')->assertRedirect(route('register'))->assertSessionHas('intent', 'seller');
        $this->get('/start/buyer')->assertRedirect(route('register'))->assertSessionHas('intent', 'buyer');
    }

    public function test_only_buyer_and_seller_are_valid(): void
    {
        $this->get('/start/admin')->assertNotFound();
    }

    public function test_a_new_seller_goes_to_create_a_brand(): void
    {
        $this->get('/start/seller');
        $this->post('/register', $this->details())->assertRedirect(route('seller.create'));
    }

    public function test_a_new_buyer_goes_home_and_the_role_stays_buyer(): void
    {
        $this->get('/start/buyer');
        $this->post('/register', $this->details())->assertRedirect('/');

        $this->assertSame('buyer', User::where('email', 'new@example.com')->first()->role);
    }

    public function test_choosing_seller_does_not_give_a_seller_role(): void
    {
        $this->get('/start/seller');
        $this->post('/register', $this->details());

        $this->assertSame('buyer', User::where('email', 'new@example.com')->first()->role);
    }

    public function test_logged_in_users_are_sent_to_their_own_pages(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $this->actingAs($buyer)->get('/')->assertRedirect(route('dashboard'));
        $this->actingAs($buyer)->get('/dashboard')->assertRedirect(route('market.index'));

        $seller = User::factory()->create(['role' => 'buyer']);
        Brand::create(['user_id' => $seller->id, 'name' => 'Flow Brand', 'district' => 'Galle']);
        $this->actingAs($seller)->get('/dashboard')->assertRedirect(route('seller.dashboard'));
    }
}