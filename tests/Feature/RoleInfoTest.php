<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleInfoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_read_role_info(): void
    {
        $this->getJson('/me/role')->assertUnauthorized();
    }

    public function test_a_buyer_has_no_brand(): void
    {
        $this->actingAs(User::factory()->create())->getJson('/me/role')->assertOk()->assertJson(['brand' => null]);
    }

    public function test_a_seller_gets_their_brand(): void
    {
        $user = User::factory()->create();
        Brand::create(['user_id' => $user->id, 'name' => 'Ceylon Gold', 'district' => 'Galle']);

        $this->actingAs($user)->getJson('/me/role')->assertOk()->assertJsonPath('brand.name', 'Ceylon Gold');
    }
}