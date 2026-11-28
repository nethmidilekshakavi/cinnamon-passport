<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandLogoController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $brand = $request->user()->brand;

        if ($brand->logo_path) {
            Storage::disk('public')->delete($brand->logo_path);
        }

        $brand->logo_path = $request->file('logo')->store('brand-logos', 'public');
        $brand->save();

        return back();
    }

    public function destroy(Request $request): RedirectResponse
    {
        $brand = $request->user()->brand;

        if ($brand->logo_path) {
            Storage::disk('public')->delete($brand->logo_path);
        }

        $brand->logo_path = null;
        $brand->save();

        return back();
    }
}