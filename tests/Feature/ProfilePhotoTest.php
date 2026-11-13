<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('a user can upload a profile photo', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/profile/photo', ['photo' => UploadedFile::fake()->image('me.jpg', 200, 200)])
        ->assertSessionHasNoErrors();

    $user->refresh();
    expect($user->photo_path)->not->toBeNull();
    expect($user->photo_url)->toBe('/storage/'.$user->photo_path);
    Storage::disk('public')->assertExists($user->photo_path);
});

test('a file that is not an image is refused', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/profile/photo', ['photo' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf')])
        ->assertSessionHasErrors('photo');

    expect($user->refresh()->photo_path)->toBeNull();
});

test('a photo over 2 MB is refused', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/profile/photo', ['photo' => UploadedFile::fake()->image('big.jpg')->size(3000)])
        ->assertSessionHasErrors('photo');
});

test('a new photo replaces the old one', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)->post('/profile/photo', ['photo' => UploadedFile::fake()->image('one.jpg')]);
    $first = $user->refresh()->photo_path;

    $this->actingAs($user)->post('/profile/photo', ['photo' => UploadedFile::fake()->image('two.jpg')]);

    Storage::disk('public')->assertMissing($first);
    Storage::disk('public')->assertExists($user->refresh()->photo_path);
});

test('the photo can be removed', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)->post('/profile/photo', ['photo' => UploadedFile::fake()->image('me.jpg')]);
    $path = $user->refresh()->photo_path;

    $this->actingAs($user)->delete('/profile/photo');

    expect($user->refresh()->photo_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('guests cannot upload a photo', function () {
    $this->post('/profile/photo', [])->assertRedirect('/login');
});
