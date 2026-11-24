<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private function order(string $status = 'delivered'): Order
    {
        $seller = User::factory()->create();
        $brand = Brand::create(['user_id' => $seller->id, 'name' => 'Ceylon Gold '.uniqid(), 'district' => 'Galle']);
        $category = Category::firstOrCreate(['slug' => 'cinnamon-sticks'], ['name' => 'Cinnamon sticks']);
        $product = $brand->products()->create([
            'category_id' => $category->id, 'name' => 'Alba Sticks', 'quality_level' => 'high',
            'ingredients' => '100% Ceylon cinnamon', 'net_quantity' => 100, 'price' => 10, 'stock' => 5, 'is_published' => true,
        ]);

        return Order::create([
            'user_id' => User::factory()->create()->id, 'brand_id' => $brand->id, 'product_id' => $product->id,
            'product_name' => 'Alba Sticks', 'product_size' => '100 g', 'unit_price' => 10, 'quantity' => 1, 'total' => 10,
            'shipping_name' => 'Emma', 'shipping_address' => '1 St', 'shipping_city' => 'London', 'shipping_country' => 'UK',
            'status' => $status, 'placed_at' => now(),
        ]);
    }

    public function test_buyer_can_rate_after_delivery(): void
    {
        $order = $this->order();

        $this->actingAs($order->buyer)
            ->post('/orders/'.$order->id.'/review', ['rating' => 5, 'comment' => 'Lovely'])
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', ['order_id' => $order->id, 'brand_id' => $order->brand_id, 'rating' => 5]);
    }

    public function test_cannot_rate_before_delivery(): void
    {
        foreach (['placed', 'shipped', 'cancelled'] as $status) {
            $order = $this->order($status);
            $this->actingAs($order->buyer)
                ->post('/orders/'.$order->id.'/review', ['rating' => 4])
                ->assertSessionHasErrors('review');
        }

        $this->assertSame(0, Review::count());
    }

    public function test_only_one_rating_per_order(): void
    {
        $order = $this->order();
        $this->actingAs($order->buyer)->post('/orders/'.$order->id.'/review', ['rating' => 4]);
        $this->actingAs($order->buyer)->post('/orders/'.$order->id.'/review', ['rating' => 1])->assertSessionHasErrors('review');

        $this->assertSame(1, Review::count());
    }

    public function test_someone_else_cannot_rate_the_order(): void
    {
        $order = $this->order();

        $this->actingAs(User::factory()->create())
            ->post('/orders/'.$order->id.'/review', ['rating' => 5])
            ->assertForbidden();
    }

    public function test_rating_must_be_one_to_five(): void
    {
        $order = $this->order();

        $this->actingAs($order->buyer)->post('/orders/'.$order->id.'/review', ['rating' => 0])->assertSessionHasErrors('rating');
        $this->actingAs($order->buyer)->post('/orders/'.$order->id.'/review', ['rating' => 6])->assertSessionHasErrors('rating');
        $this->actingAs($order->buyer)->post('/orders/'.$order->id.'/review', [])->assertSessionHasErrors('rating');
    }

    public function test_guests_cannot_rate(): void
    {
        $order = $this->order();

        $this->post('/orders/'.$order->id.'/review', ['rating' => 5])->assertRedirect('/login');
    }

    public function test_product_page_shows_the_average_rating(): void
    {
        $order = $this->order();
        $other = $this->order();
        Review::create(['order_id' => $order->id, 'user_id' => $order->user_id, 'brand_id' => $order->brand_id, 'rating' => 5]);
        Review::create(['order_id' => $other->id, 'user_id' => $other->user_id, 'brand_id' => $order->brand_id, 'rating' => 4]);

        $this->get('/market/'.$order->product->slug)->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('rating.count', 2)
            ->where('rating.average', 4.5)
            ->has('reviews', 2));
    }

    public function test_order_page_tells_whether_it_can_be_rated(): void
    {
        $order = $this->order();

        $this->actingAs($order->buyer)->get('/orders/'.$order->id)
            ->assertInertia(fn (Assert $page) => $page->where('canReview', true));

        $this->actingAs($order->buyer)->post('/orders/'.$order->id.'/review', ['rating' => 3]);

        $this->actingAs($order->buyer)->get('/orders/'.$order->id)
            ->assertInertia(fn (Assert $page) => $page->where('canReview', false)->where('review.rating', 3));
    }
}