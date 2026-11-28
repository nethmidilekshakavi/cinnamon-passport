<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_read_stats(): void
    {
        $this->getJson('/seller/stats')->assertUnauthorized();
    }

    public function test_a_user_without_a_brand_is_sent_to_create_one(): void
    {
        $this->actingAs(User::factory()->create())->get('/seller/stats')->assertRedirect(route('seller.create'));
    }

    public function test_a_seller_gets_their_own_numbers(): void
    {
        $seller = User::factory()->create();
        $brand = Brand::create(['user_id' => $seller->id, 'name' => 'Stats Brand', 'district' => 'Galle']);

        $this->actingAs($seller)->getJson('/seller/stats')->assertOk()->assertJson([
            'orders' => ['total' => 0, 'to_ship' => 0],
            'revenue' => 0,
            'rating' => ['average' => 0, 'count' => 0],
        ]);

        $this->assertSame('Stats Brand', $brand->name);
    }

    public function test_the_dashboard_page_still_opens(): void
    {
        $seller = User::factory()->create();
        Brand::create(['user_id' => $seller->id, 'name' => 'Stats Brand', 'district' => 'Galle']);

        $this->actingAs($seller)->get('/seller/dashboard')->assertOk();
    }
}