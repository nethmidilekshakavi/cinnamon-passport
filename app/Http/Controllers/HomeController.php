<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        // The intro uses the first three .mp4 files (alphabetical) in public/videos.
        $videos = collect(glob(public_path('videos/*.mp4')) ?: [])
            ->map(fn ($path) => '/videos/'.basename($path))
            ->sort()
            ->take(3)
            ->values()
            ->all();

        $featured = Product::query()
            ->published()
            ->with(['brand:id,name,slug,logo_path,district,country', 'category:id,name,slug'])
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return Inertia::render('Welcome', [
            'videos' => $videos,
            'featured' => $featured,
            'stats' => [
                'products' => Product::published()->count(),
                'brands' => Brand::whereHas('products', fn ($q) => $q->published())->count(),
            ],
        ]);
    }
}