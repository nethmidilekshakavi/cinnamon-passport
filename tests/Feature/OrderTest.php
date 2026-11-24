<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private function brand(string $name = 'Ceylon Gold'): Brand
    {
        $user = User::factory()->create();

        return Brand::create(['user_id' => $user->id, 'name' => $name, 'district' => 'Galle']);
    }

    private function product(Brand $brand, array $override = []): Product
    {
        $category = Category::firstOrCreate(['slug' => 'cinnamon-sticks'], ['name' => 'Cinnamon sticks']);

        return $brand->products()->create(array_merge([
            'category_id' => $category->id,
            'name' => 'Alba Sticks',
            'quality_level' => 'high',
            'grade' => 'Alba',
            'ingredients' => '100% Ceylon cinnamon',
            'net_quantity' => 100,
            'price' => 10,
            'stock' => 5,
            'is_published' => true,
        ], $override));
    }

    private function details(array $override = []): array
    {
        return array_merge([
            'quantity' => 2,
            'shipping_name' => 'Emma Buyer',
            'shipping_address' => '12 High Street',
            'shipping_city' => 'London',
            'shipping_postal_code' => 'N1 1AA',
            'shipping_country' => 'United Kingdom',
            'accept_prototype' => 1,
        ], $override);
    }

    private function placeOrder(User $buyer, Product $product, array $override = []): Order
    {
        $this->actingAs($buyer)
            ->post('/checkout/'.$product->slug, $this->details($override))
            ->assertRedirect();

        return Order::orderByDesc('id')->firstOrFail();
    }

    public function test_guests_must_log_in_to_order_or_see_orders(): void
    {
        $product = $this->product($this->brand());

        $this->get('/checkout/'.$product->slug)->assertRedirect('/login');
        $this->post('/checkout/'.$product->slug, $this->details())->assertRedirect('/login');
        $this->get('/orders')->assertRedirect('/login');
    }

    public function test_checkout_page_opens_for_a_buyer(): void
    {
        $product = $this->product($this->brand());

        $this->actingAs(User::factory()->create())
            ->get('/checkout/'.$product->slug)
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Checkout/Create')
                ->where('maxQuantity', 5));
    }

    public function test_a_seller_cannot_order_their_own_product(): void
    {
        $brand = $this->brand();
        $product = $this->product($brand);

        $this->actingAs($brand->user)
            ->get('/checkout/'.$product->slug)
            ->assertRedirect(route('market.show', $product->slug));

        $this->actingAs($brand->user)
            ->post('/checkout/'.$product->slug, $this->details())
            ->assertSessionHasErrors('quantity');

        $this->assertSame(0, Order::count());
    }

    public function test_unavailable_products_cannot_be_ordered(): void
    {
        $brand = $this->brand();
        $buyer = User::factory()->create();

        $soldOut = $this->product($brand, ['name' => 'Sold out', 'stock' => 0]);
        $expired = $this->product($brand, ['name' => 'Expired', 'expiry_date' => '2020-01-01']);
        $hidden = $this->product($brand, ['name' => 'Hidden', 'is_published' => false]);

        $this->actingAs($buyer)->post('/checkout/'.$soldOut->slug, $this->details())->assertSessionHasErrors('quantity');
        $this->actingAs($buyer)->post('/checkout/'.$expired->slug, $this->details())->assertSessionHasErrors('quantity');
        $this->actingAs($buyer)->get('/checkout/'.$hidden->slug)->assertNotFound();

        $this->assertSame(0, Order::count());
    }

    public function test_a_buyer_can_place_an_order(): void
    {
        $brand = $this->brand();
        $product = $this->product($brand);
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->post('/checkout/'.$product->slug, $this->details())
            ->assertRedirectContains('/orders/');

        $order = Order::firstOrFail();

        $this->assertSame($buyer->id, $order->user_id);
        $this->assertSame($brand->id, $order->brand_id);
        $this->assertSame('placed', $order->status);
        $this->assertSame('Alba Sticks', $order->product_name);
        $this->assertSame('100 g', $order->product_size);
        $this->assertSame(2, $order->quantity);
        $this->assertSame('20.00', $order->total);
        $this->assertSame('CT-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT), $order->order_number);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_quantity_cannot_exceed_stock(): void
    {
        $product = $this->product($this->brand());

        $this->actingAs(User::factory()->create())
            ->post('/checkout/'.$product->slug, $this->details(['quantity' => 6]))
            ->assertSessionHasErrors('quantity');

        $this->assertSame(0, Order::count());
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_the_prototype_notice_must_be_accepted(): void
    {
        $product = $this->product($this->brand());

        $this->actingAs(User::factory()->create())
            ->post('/checkout/'.$product->slug, $this->details(['accept_prototype' => 0]))
            ->assertSessionHasErrors('accept_prototype');

        $this->assertSame(0, Order::count());
    }

    public function test_shipping_details_are_required(): void
    {
        $product = $this->product($this->brand());

        $this->actingAs(User::factory()->create())
            ->post('/checkout/'.$product->slug, $this->details(['shipping_name' => '', 'shipping_country' => '']))
            ->assertSessionHasErrors(['shipping_name', 'shipping_country']);
    }

    public function test_an_order_keeps_the_price_from_the_time_of_ordering(): void
    {
        $product = $this->product($this->brand());
        $order = $this->placeOrder(User::factory()->create(), $product);

        $product->update(['price' => 99]);

        $this->assertSame('10.00', $order->fresh()->unit_price);
        $this->assertSame('20.00', $order->fresh()->total);
    }

    public function test_a_buyer_sees_only_their_own_orders(): void
    {
        $product = $this->product($this->brand(), ['stock' => 10]);
        $one = User::factory()->create();
        $two = User::factory()->create();

        $this->placeOrder($one, $product);
        $this->placeOrder($two, $product);

        $this->actingAs($one)->get('/orders')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Orders/Index')
            ->has('orders', 1));
    }

    public function test_a_buyer_cannot_open_someone_elses_order(): void
    {
        $product = $this->product($this->brand());
        $order = $this->placeOrder(User::factory()->create(), $product);

        $this->actingAs(User::factory()->create())->get('/orders/'.$order->id)->assertForbidden();
        $this->actingAs($order->buyer)->get('/orders/'.$order->id)->assertOk();
    }

    public function test_a_buyer_can_cancel_and_the_stock_comes_back(): void
    {
        $product = $this->product($this->brand());
        $buyer = User::factory()->create();
        $order = $this->placeOrder($buyer, $product);

        $this->assertSame(3, $product->fresh()->stock);

        $this->actingAs($buyer)->post('/orders/'.$order->id.'/cancel')->assertRedirect();

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->cancelled_at);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_an_order_cannot_be_cancelled_after_it_shipped(): void
    {
        $brand = $this->brand();
        $buyer = User::factory()->create();
        $order = $this->placeOrder($buyer, $this->product($brand));

        $this->actingAs($brand->user)->post('/seller/orders/'.$order->id.'/ship');

        $this->actingAs($buyer)->post('/orders/'.$order->id.'/cancel')->assertSessionHasErrors('order');
        $this->assertSame('shipped', $order->fresh()->status);
    }

    public function test_a_seller_sees_only_their_own_brands_orders(): void
    {
        $mine = $this->brand('Mine');
        $theirs = $this->brand('Theirs');
        $buyer = User::factory()->create();

        $this->placeOrder($buyer, $this->product($mine));
        $this->placeOrder($buyer, $this->product($theirs));

        $this->actingAs($mine->user)->get('/seller/orders')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Seller/Orders/Index')
            ->has('orders', 1));
    }

    public function test_a_seller_can_ship_a_new_order(): void
    {
        $brand = $this->brand();
        $order = $this->placeOrder(User::factory()->create(), $this->product($brand));

        $this->actingAs($brand->user)->post('/seller/orders/'.$order->id.'/ship')->assertRedirect();

        $this->assertSame('shipped', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->shipped_at);
    }

    public function test_another_seller_cannot_ship_the_order(): void
    {
        $brand = $this->brand('Mine');
        $other = $this->brand('Other');
        $order = $this->placeOrder(User::factory()->create(), $this->product($brand));

        $this->actingAs($other->user)->post('/seller/orders/'.$order->id.'/ship')->assertForbidden();
        $this->assertSame('placed', $order->fresh()->status);
    }

    public function test_a_cancelled_order_cannot_be_shipped(): void
    {
        $brand = $this->brand();
        $buyer = User::factory()->create();
        $order = $this->placeOrder($buyer, $this->product($brand));

        $this->actingAs($buyer)->post('/orders/'.$order->id.'/cancel');

        $this->actingAs($brand->user)->post('/seller/orders/'.$order->id.'/ship')->assertSessionHasErrors('order');
        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_seller_orders_page_needs_a_brand(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/seller/orders')
            ->assertRedirect(route('seller.create'));
    }

    public function test_a_buyer_confirms_receipt_only_after_shipping(): void
    {
        $brand = $this->brand();
        $buyer = User::factory()->create();
        $order = $this->placeOrder($buyer, $this->product($brand));

        $this->actingAs($buyer)->post('/orders/'.$order->id.'/received')->assertSessionHasErrors('order');
        $this->assertSame('placed', $order->fresh()->status);

        $this->actingAs($brand->user)->post('/seller/orders/'.$order->id.'/ship');
        $this->actingAs($buyer)->post('/orders/'.$order->id.'/received')->assertRedirect();

        $this->assertSame('delivered', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->delivered_at);
    }

    public function test_product_page_says_whether_it_can_be_ordered(): void
    {
        $brand = $this->brand();
        $product = $this->product($brand);
        $soldOut = $this->product($brand, ['name' => 'Sold out', 'stock' => 0]);

        $this->get('/market/'.$product->slug)->assertInertia(fn (Assert $page) => $page->where('canOrder', true));
        $this->get('/market/'.$soldOut->slug)->assertInertia(fn (Assert $page) => $page->where('canOrder', false));
        $this->actingAs($brand->user)->get('/market/'.$product->slug)
            ->assertInertia(fn (Assert $page) => $page->where('canOrder', false));
    }

    public function test_deleting_a_product_keeps_the_order(): void
    {
        $product = $this->product($this->brand());
        $order = $this->placeOrder(User::factory()->create(), $product);

        $product->delete();

        $order = $order->fresh();
        $this->assertNull($order->product_id);
        $this->assertSame('Alba Sticks', $order->product_name);
    }
}