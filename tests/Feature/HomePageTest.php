<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    private function brand(): Brand
    {
        $user = User::factory()->create();

        return Brand::create(['user_id' => $user->id, 'name' => 'Ceylon Gold', 'district' => 'Galle']);
    }

    private function product(Brand $brand, array $override = []): void
    {
        $category = Category::firstOrCreate(['slug' => 'cinnamon-sticks'], ['name' => 'Cinnamon sticks']);

        $brand->products()->create(array_merge([
            'category_id' => $category->id,
            'name' => 'Alba Sticks',
            'quality_level' => 'high',
            'ingredients' => '100% Ceylon cinnamon',
            'net_quantity' => 100,
            'price' => 10,
            'stock' => 5,
            'is_published' => true,
        ], $override));
    }

    public function test_home_page_is_public_and_works_with_no_products(): void
    {
        $this->get('/')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('featured', 0)
            ->where('stats.products', 0)
            ->has('videos'));
    }

    public function test_home_page_features_only_published_products_and_at_most_four(): void
    {
        $brand = $this->brand();
        foreach (range(1, 5) as $i) {
            $this->product($brand, ['name' => 'Item '.$i]);
        }
        $this->product($brand, ['name' => 'Hidden', 'is_published' => false]);

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->has('featured', 4)
            ->where('stats.products', 5)
            ->where('stats.brands', 1));
    }
}