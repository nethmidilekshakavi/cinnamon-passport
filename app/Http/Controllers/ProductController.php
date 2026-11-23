<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $products = $request->user()->brand
            ->products()
            ->with('category:id,name')
            ->latest()
            ->get();

        return Inertia::render('Seller/Products/Index', ['products' => $products]);
    }

    public function create(): Response
    {
        return Inertia::render('Seller/Products/Create', ['options' => $this->options()]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $this->payload($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $request->user()->brand->products()->create($data);

        return redirect()->route('seller.products.index');
    }

    public function edit(Request $request, Product $product): Response
    {
        $this->ownProduct($request, $product);

        return Inertia::render('Seller/Products/Edit', [
            'product' => $product,
            'options' => $this->options(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $this->payload($request);

        if ($request->hasFile('image')) {
            $this->deleteImage($product);
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('seller.products.index');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $this->ownProduct($request, $product);

        $this->deleteImage($product);
        $product->delete();

        return redirect()->route('seller.products.index');
    }

    private function payload(ProductRequest $request): array
    {
        $data = $request->safe()->except('image');
        $data['is_organic'] = $request->boolean('is_organic');
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }

    private function ownProduct(Request $request, Product $product): void
    {
        abort_unless($product->brand_id === $request->user()->brand->id, 403);
    }

    private function deleteImage(Product $product): void
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
    }

    private function options(): array
    {
        return [
            'categories' => Category::orderBy('id')->get(['id', 'name']),
            'qualityLevels' => Product::QUALITY_LEVELS,
            'grades' => Product::GRADES,
            'growingMethods' => Product::GROWING_METHODS,
            'units' => Product::UNITS,
        ];
    }
}