<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketController extends Controller
{
    private const SORTS = ['newest', 'price_asc', 'price_desc'];

    private const BRAND_COLUMNS = 'brand:id,name,slug,district,country';

    public function index(Request $request): Response
    {
        $filters = [
            'q' => $this->text($request, 'q'),
            'category' => $this->text($request, 'category'),
            'quality' => $this->text($request, 'quality'),
            'grade' => $this->text($request, 'grade'),
            'district' => $this->text($request, 'district'),
            'organic' => $request->boolean('organic'),
            'min_price' => $this->number($request, 'min_price'),
            'max_price' => $this->number($request, 'max_price'),
            'sort' => in_array($this->text($request, 'sort'), self::SORTS, true)
                ? $this->text($request, 'sort')
                : 'newest',
        ];

        $query = Product::query()
            ->published()
            ->with([self::BRAND_COLUMNS, 'category:id,name,slug'])
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $like = '%'.$filters['q'].'%';
                $query->where(fn ($w) => $w
                    ->where('name', 'like', $like)
                    ->orWhere('ingredients', 'like', $like)
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', $like)));
            })
            ->when($filters['category'] !== '', fn ($query) => $query->whereHas(
                'category', fn ($c) => $c->where('slug', $filters['category'])
            ))
            ->when(in_array($filters['quality'], Product::QUALITY_LEVELS, true),
                fn ($query) => $query->where('quality_level', $filters['quality']))
            ->when(in_array($filters['grade'], Product::GRADES, true),
                fn ($query) => $query->where('grade', $filters['grade']))
            ->when(in_array($filters['district'], Brand::DISTRICTS, true),
                fn ($query) => $query->whereHas('brand', fn ($b) => $b->where('district', $filters['district'])))
            ->when($filters['organic'], fn ($query) => $query->where('is_organic', true))
            ->when($filters['min_price'] !== '', fn ($query) => $query->where('price', '>=', $filters['min_price']))
            ->when($filters['max_price'] !== '', fn ($query) => $query->where('price', '<=', $filters['max_price']));

        match ($filters['sort']) {
            'price_asc' => $query->orderBy('price')->orderByDesc('id'),
            'price_desc' => $query->orderByDesc('price')->orderByDesc('id'),
            default => $query->orderByDesc('id'),
        };

        return Inertia::render('Market/Index', [
            'products' => $query->paginate(12)->withQueryString(),
            'filters' => $filters,
            'options' => [
                'categories' => Category::orderBy('id')->get(['id', 'name', 'slug']),
                'qualityLevels' => Product::QUALITY_LEVELS,
                'grades' => Product::GRADES,
                'districts' => Brand::DISTRICTS,
            ],
        ]);
    }

    public function show(Request $request, Product $product): Response
    {
        $isOwner = $request->user()?->brand?->id === $product->brand_id;

        abort_unless($product->is_published || $isOwner, 404);

        $product->load(['brand:id,name,slug,district,country,description', 'category:id,name,slug']);

        $more = Product::query()
            ->published()
            ->where('brand_id', $product->brand_id)
            ->where('id', '!=', $product->id)
            ->with([self::BRAND_COLUMNS, 'category:id,name,slug'])
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return Inertia::render('Market/Show', [
            'product' => $product,
            'isExpired' => $product->isExpired(),
            'isOwner' => $isOwner,
            'more' => $more,
        ]);
    }

    public function brand(Brand $brand): Response
    {
        $products = $brand->products()
            ->published()
            ->with([self::BRAND_COLUMNS, 'category:id,name,slug'])
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Market/Brand', [
            'brand' => $brand->only(['id', 'name', 'slug', 'description', 'district', 'country', 'website']),
            'products' => $products,
        ]);
    }

    private function text(Request $request, string $key): string
    {
        $value = $request->query($key);

        return is_string($value) ? trim($value) : '';
    }

    private function number(Request $request, string $key): float|string
    {
        $value = $request->query($key);

        return is_numeric($value) ? (float) $value : '';
    }
}