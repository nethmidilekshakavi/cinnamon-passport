<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    public function create(Request $request): Response|RedirectResponse
    {
        if ($request->user()->brand) {
            return redirect()->route('seller.dashboard');
        }

        return Inertia::render('Seller/CreateBrand', [
            'districts' => Brand::DISTRICTS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->brand) {
            return redirect()->route('seller.dashboard');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:brands,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'district' => ['required', Rule::in(Brand::DISTRICTS)],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);

        $user->brand()->create($data);

        return redirect()->route('seller.dashboard');
    }

    public function dashboard(Request $request): Response|RedirectResponse
    {
        $brand = $request->user()->brand;

        if (! $brand) {
            return redirect()->route('seller.create');
        }

        $brand->loadCount('products');

        return Inertia::render('Seller/Dashboard', ['brand' => $brand]);
    }
}