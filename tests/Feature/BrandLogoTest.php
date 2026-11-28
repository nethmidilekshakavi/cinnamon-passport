<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandLogoTest extends TestCase
{
    use RefreshDatabase;

    private function seller(): User
    {
        $user = User::factory()->create();
        Brand::create(['user_id' => $user->id, 'name' => 'Logo Brand', 'district' => 'Galle']);

        return $user;
    }

    public function test_a_seller_can_upload_a_logo(): void
    {
        Storage::fake('public');
        $user = $this->seller();

        $this->actingAs($user)
            ->post('/seller/logo', ['logo' => UploadedFile::fake()->image('logo.png', 200, 200)])
            ->assertRedirect();

        $path = $user->brand->fresh()->logo_path;
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_a_new_logo_replaces_the_old_one(): void
    {
        Storage::fake('public');
        $user = $this->seller();

        $this->actingAs($user)->post('/seller/logo', ['logo' => UploadedFile::fake()->image('one.png')]);
        $first = $user->brand->fresh()->logo_path;

        $this->actingAs($user)->post('/seller/logo', ['logo' => UploadedFile::fake()->image('two.png')]);

        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($user->brand->fresh()->logo_path);
    }

    public function test_a_file_that_is_not_an_image_is_refused(): void
    {
        Storage::fake('public');
        $user = $this->seller();

        $this->actingAs($user)
            ->post('/seller/logo', ['logo' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')])
            ->assertSessionHasErrors('logo');
    }

    public function test_a_logo_over_two_megabytes_is_refused(): void
    {
        Storage::fake('public');
        $user = $this->seller();

        $this->actingAs($user)
            ->post('/seller/logo', ['logo' => UploadedFile::fake()->image('big.png')->size(3000)])
            ->assertSessionHasErrors('logo');
    }

    public function test_the_logo_can_be_removed(): void
    {
        Storage::fake('public');
        $user = $this->seller();

        $this->actingAs($user)->post('/seller/logo', ['logo' => UploadedFile::fake()->image('logo.png')]);
        $path = $user->brand->fresh()->logo_path;

        $this->actingAs($user)->delete('/seller/logo')->assertRedirect();

        $this->assertNull($user->brand->fresh()->logo_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_guests_and_users_without_a_brand_cannot_upload(): void
    {
        $this->post('/seller/logo', [])->assertRedirect('/login');

        $this->actingAs(User::factory()->create())
            ->post('/seller/logo', ['logo' => UploadedFile::fake()->image('logo.png')])
            ->assertRedirect(route('seller.create'));
    }
}