<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/sell')->assertRedirect('/login');
    }

    public function test_user_can_open_the_form(): void
    {
        $this->actingAs(User::factory()->create())->get('/sell')->assertOk();
    }

    public function test_user_can_create_a_brand(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/sell', [
            'name' => 'Ceylon Gold',
            'district' => 'Galle',
        ])->assertRedirect(route('seller.dashboard'));

        $this->assertDatabaseHas('brands', [
            'user_id' => $user->id,
            'name' => 'Ceylon Gold',
            'slug' => 'ceylon-gold',
        ]);
    }

    public function test_user_cannot_create_a_second_brand(): void
    {
        $user = User::factory()->create();
        Brand::create(['user_id' => $user->id, 'name' => 'First', 'district' => 'Galle']);

        $this->actingAs($user)->post('/sell', [
            'name' => 'Second',
            'district' => 'Kandy',
        ])->assertRedirect(route('seller.dashboard'));

        $this->assertSame(1, Brand::count());
    }

    public function test_name_and_valid_district_are_required(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/sell', ['name' => '', 'district' => 'Atlantis'])
            ->assertSessionHasErrors(['name', 'district']);
    }

    public function test_dashboard_redirects_when_user_has_no_brand(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/seller/dashboard')
            ->assertRedirect(route('seller.create'));
    }
}