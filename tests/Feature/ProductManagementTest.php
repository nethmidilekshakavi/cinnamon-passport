<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    private function sellerWithBrand(string $name = 'Ceylon Gold'): array
    {
        $user = User::factory()->create();
        $brand = Brand::create(['user_id' => $user->id, 'name' => $name, 'district' => 'Galle']);

        return [$user, $brand];
    }

    private function category(): Category
    {
        return Category::firstOrCreate(['slug' => 'cinnamon-sticks'], ['name' => 'Cinnamon sticks']);
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'category_id' => $this->category()->id,
            'name' => 'Alba Sticks 100g',
            'quality_level' => 'high',
            'grade' => 'Alba',
            'ingredients' => '100% Ceylon cinnamon',
            'net_quantity' => 100,
            'unit' => 'g',
            'price' => 12.5,
            'stock' => 20,
            'is_organic' => 1,
            'growing_method' => 'organic',
            'is_published' => 1,
        ], $override);
    }

    private function makeProduct(Brand $brand): Product
    {
        return $brand->products()->create([
            'category_id' => $this->category()->id,
            'name' => 'Existing Product',
            'quality_level' => 'medium',
            'ingredients' => 'Cinnamon',
            'net_quantity' => 50,
            'price' => 5,
            'stock' => 3,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/seller/products')->assertRedirect('/login');
    }

    public function test_user_without_brand_is_sent_to_create_a_brand(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/seller/products')
            ->assertRedirect(route('seller.create'));
    }

    public function test_seller_can_see_the_products_page(): void
    {
        [$user] = $this->sellerWithBrand();

        $this->actingAs($user)->get('/seller/products')->assertOk();
        $this->actingAs($user)->get('/seller/products/create')->assertOk();
    }

    public function test_seller_can_add_a_product(): void
    {
        [$user, $brand] = $this->sellerWithBrand();

        $this->actingAs($user)
            ->post('/seller/products', $this->payload())
            ->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseHas('products', [
            'brand_id' => $brand->id,
            'name' => 'Alba Sticks 100g',
            'slug' => 'alba-sticks-100g',
            'quality_level' => 'high',
        ]);
    }

    public function test_seller_can_upload_a_product_photo(): void
    {
        Storage::fake('public');
        [$user, $brand] = $this->sellerWithBrand();

        $this->actingAs($user)
            ->post('/seller/products', $this->payload(['image' => UploadedFile::fake()->image('p.jpg')]))
            ->assertRedirect(route('seller.products.index'));

        $product = $brand->products()->first();
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_expiry_cannot_be_before_production(): void
    {
        [$user] = $this->sellerWithBrand();

        $this->actingAs($user)
            ->post('/seller/products', $this->payload([
                'production_date' => '2026-06-10',
                'expiry_date' => '2026-06-01',
            ]))
            ->assertSessionHasErrors('expiry_date');
    }

    public function test_invalid_quality_level_is_rejected(): void
    {
        [$user] = $this->sellerWithBrand();

        $this->actingAs($user)
            ->post('/seller/products', $this->payload(['quality_level' => 'amazing']))
            ->assertSessionHasErrors('quality_level');
    }

    public function test_seller_can_update_own_product(): void
    {
        [$user, $brand] = $this->sellerWithBrand();
        $product = $this->makeProduct($brand);

        $this->actingAs($user)
            ->put('/seller/products/'.$product->id, $this->payload(['name' => 'Renamed', 'price' => 9.99]))
            ->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Renamed', 'price' => 9.99]);
    }

    public function test_seller_cannot_touch_another_brands_product(): void
    {
        [$user] = $this->sellerWithBrand('Mine');
        [, $otherBrand] = $this->sellerWithBrand('Theirs');
        $theirs = $this->makeProduct($otherBrand);

        $this->actingAs($user)->get('/seller/products/'.$theirs->id.'/edit')->assertForbidden();
        $this->actingAs($user)->put('/seller/products/'.$theirs->id, $this->payload())->assertForbidden();
        $this->actingAs($user)->delete('/seller/products/'.$theirs->id)->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $theirs->id, 'name' => 'Existing Product']);
    }

    public function test_seller_can_delete_own_product(): void
    {
        [$user, $brand] = $this->sellerWithBrand();
        $product = $this->makeProduct($brand);

        $this->actingAs($user)
            ->delete('/seller/products/'.$product->id)
            ->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}