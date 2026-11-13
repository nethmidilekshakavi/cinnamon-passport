<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $this->forget($user);

        $user->photo_path = $request->file('photo')->store('avatars', 'public');
        $user->save();

        return back();
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->forget($user);

        $user->photo_path = null;
        $user->save();

        return back();
    }

    private function forget(User $user): void
    {
        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }
    }
}
