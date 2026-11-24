<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MarketTest extends TestCase
{
    use RefreshDatabase;

    private function brand(string $name = 'Ceylon Gold', string $district = 'Galle'): Brand
    {
        $user = User::factory()->create();

        return Brand::create(['user_id' => $user->id, 'name' => $name, 'district' => $district]);
    }

    private function category(string $name = 'Cinnamon sticks'): Category
    {
        return Category::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
    }

    private function product(Brand $brand, array $override = []): Product
    {
        return $brand->products()->create(array_merge([
            'category_id' => $this->category()->id,
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

    private function assertOnly(string $url, string $name): void
    {
        $this->get($url)->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Market/Index')
            ->has('products.data', 1)
            ->where('products.data.0.name', $name));
    }

    public function test_market_is_public_and_lists_only_published_products(): void
    {
        $brand = $this->brand();
        $this->product($brand, ['name' => 'Visible']);
        $this->product($brand, ['name' => 'Hidden', 'is_published' => false]);

        $this->get('/market')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Market/Index')
            ->has('products.data', 1)
            ->where('products.data.0.name', 'Visible'));
    }

    public function test_filter_by_category(): void
    {
        $brand = $this->brand();
        $this->product($brand, ['name' => 'Sticks one']);
        $this->product($brand, ['name' => 'Powder one', 'category_id' => $this->category('Cinnamon powder')->id]);

        $this->assertOnly('/market?category=cinnamon-powder', 'Powder one');
    }

    public function test_filters_for_quality_grade_district_organic_and_price(): void
    {
        $galle = $this->brand('Galle Spice', 'Galle');
        $kandy = $this->brand('Kandy Spice', 'Kandy');

        $this->product($galle, [
            'name' => 'P1', 'quality_level' => 'high', 'grade' => 'Alba',
            'is_organic' => true, 'price' => 20,
        ]);
        $this->product($kandy, [
            'name' => 'P2', 'quality_level' => 'low', 'grade' => 'H1',
            'is_organic' => false, 'price' => 5,
        ]);

        $this->assertOnly('/market?quality=low', 'P2');
        $this->assertOnly('/market?grade=Alba', 'P1');
        $this->assertOnly('/market?district=Kandy', 'P2');
        $this->assertOnly('/market?organic=1', 'P1');
        $this->assertOnly('/market?min_price=10', 'P1');
        $this->assertOnly('/market?max_price=10', 'P2');
    }

    public function test_search_matches_name_ingredients_and_brand(): void
    {
        $this->product($this->brand('Sunrise Spice'), ['name' => 'Tea blend', 'ingredients' => 'Cinnamon, ginger']);
        $this->product($this->brand('Other Brand'), ['name' => 'Plain sticks', 'ingredients' => 'Cinnamon']);

        $this->assertOnly('/market?q=ginger', 'Tea blend');
        $this->assertOnly('/market?q=Sunrise', 'Tea blend');
        $this->assertOnly('/market?q=Plain', 'Plain sticks');
    }

    public function test_sorting_by_price(): void
    {
        $brand = $this->brand();
        $this->product($brand, ['name' => 'Cheap', 'price' => 5]);
        $this->product($brand, ['name' => 'Pricey', 'price' => 50]);

        $this->get('/market?sort=price_asc')->assertInertia(fn (Assert $page) => $page
            ->where('products.data.0.name', 'Cheap'));
        $this->get('/market?sort=price_desc')->assertInertia(fn (Assert $page) => $page
            ->where('products.data.0.name', 'Pricey'));
        $this->get('/market')->assertInertia(fn (Assert $page) => $page
            ->where('products.data.0.name', 'Pricey'));
    }

    public function test_results_are_paginated_by_twelve(): void
    {
        $brand = $this->brand();
        foreach (range(1, 13) as $i) {
            $this->product($brand, ['name' => 'Item '.$i]);
        }

        $this->get('/market')->assertInertia(fn (Assert $page) => $page
            ->has('products.data', 12)
            ->where('products.total', 13));
    }

    public function test_bad_filter_values_are_ignored_and_do_not_crash(): void
    {
        $this->product($this->brand());

        $this->get('/market?quality=amazing&grade=Z9&district=Atlantis&sort=weird&min_price=abc')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->has('products.data', 1));

        $this->get('/market?q[]=x&category[]=y')->assertOk();
    }

    public function test_product_page_shows_a_published_product(): void
    {
        $product = $this->product($this->brand());

        $this->get('/market/'.$product->slug)->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Market/Show')
            ->where('product.name', 'Alba Sticks')
            ->where('isExpired', false));
    }

    public function test_hidden_product_is_not_found_for_the_public_but_visible_to_its_owner(): void
    {
        $brand = $this->brand();
        $hidden = $this->product($brand, ['is_published' => false]);

        $this->get('/market/'.$hidden->slug)->assertNotFound();
        $this->actingAs(User::factory()->create())->get('/market/'.$hidden->slug)->assertNotFound();
        $this->actingAs($brand->user)->get('/market/'.$hidden->slug)->assertOk();
    }

    public function test_expired_product_is_marked_expired(): void
    {
        $product = $this->product($this->brand(), ['expiry_date' => '2020-01-01']);

        $this->get('/market/'.$product->slug)->assertInertia(fn (Assert $page) => $page
            ->where('isExpired', true));
    }

    public function test_brand_page_lists_only_published_products(): void
    {
        $brand = $this->brand();
        $this->product($brand, ['name' => 'Shown']);
        $this->product($brand, ['name' => 'Not shown', 'is_published' => false]);

        $this->get('/brands/'.$brand->slug)->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Market/Brand')
            ->has('products', 1)
            ->where('products.0.name', 'Shown'));
    }

    public function test_public_pages_do_not_leak_the_sellers_account_details(): void
    {
        $brand = $this->brand();
        $product = $this->product($brand);

        $this->get('/market')->assertDontSee($brand->user->email);
        $this->get('/market/'.$product->slug)->assertDontSee($brand->user->email);
        $this->get('/brands/'.$brand->slug)->assertDontSee($brand->user->email);
    }
}