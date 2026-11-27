<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellerOrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\EnsureUserHasBrand;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\CollectorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\VerifyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\StudyController;
use App\Http\Controllers\ProfilePhotoController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Public batch verification (QR codes point here). No login needed.
Route::get('/verify', [VerifyController::class, 'index'])->name('verify.index');
Route::post('/verify', [VerifyController::class, 'lookup'])->name('verify.lookup');
Route::get('/verify/{code}', [VerifyController::class, 'show'])->name('verify.show');

// After login, each role goes to its own home page.
Route::get('/dashboard', function (Request $request) {
    return match ($request->user()->role) {
        'collector' => redirect()->route('collector.home'),
        'buyer' => (auth()->user()->brand ? redirect()->route('seller.dashboard') : redirect()->route('market.index')),
        default => redirect()->route('batches.index'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // SMS simulator: growers and collectors (role is checked in the controller).
    Route::get('/sms', [SmsController::class, 'index'])->name('sms.index');
    Route::post('/sms', [SmsController::class, 'store'])->name('sms.store');
    Route::get('/sms/guide', [SmsController::class, 'guide'])->name('sms.guide');

        Route::post('/profile/photo', [ProfilePhotoController::class, 'update'])->name('profile.photo.update');
        Route::delete('/profile/photo', [ProfilePhotoController::class, 'destroy'])->name('profile.photo.destroy');
});

// Grower area.
Route::middleware(['auth', 'role:grower'])->group(function () {
    Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/create', [BatchController::class, 'create'])->name('batches.create');
    Route::post('/batches', [BatchController::class, 'store'])->name('batches.store');
    Route::post('/batches/{batch}/handover', [BatchController::class, 'handover'])->name('batches.handover');
});

// Collector area.
Route::middleware(['auth', 'role:collector'])->group(function () {
    Route::get('/collector', [CollectorController::class, 'index'])->name('collector.home');
    Route::post('/collector/handovers/{batch}/confirm', [CollectorController::class, 'confirm'])->name('collector.confirm');
});

// Buyer home is the verification page.
Route::middleware(['auth', 'role:buyer'])
    ->get('/buyer', [VerifyController::class, 'index'])
    ->name('buyer.home');


// Usability study (RQ1)
Route::get('/study', [StudyController::class, 'index'])->name('study.index');
Route::post('/study/trials', [StudyController::class, 'store'])->name('study.store');
Route::get('/study/trials/{trial}', [StudyController::class, 'show'])->name('study.show');
Route::post('/study/trials/{trial}/start', [StudyController::class, 'start'])->name('study.start');
Route::post('/study/trials/{trial}/giveup', [StudyController::class, 'giveUp'])->name('study.giveup');
Route::get('/study/sus', [StudyController::class, 'susForm'])->name('study.sus');
Route::post('/study/sus', [StudyController::class, 'susStore'])->name('study.sus.store');
Route::get('/study/results', [StudyController::class, 'results'])->name('study.results');
Route::get('/study/export', [StudyController::class, 'export'])->name('study.export');

Route::middleware('auth')->group(function () {
    Route::get('/sell', [BrandController::class, 'create'])->name('seller.create');
    Route::post('/sell', [BrandController::class, 'store'])->name('seller.store');
    Route::get('/seller/dashboard', [BrandController::class, 'dashboard'])->name('seller.dashboard');
});
Route::middleware(['auth', EnsureUserHasBrand::class])->prefix('seller')->name('seller.')->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);
});
// Public marketplace (no login needed)
Route::get('/market', [MarketController::class, 'index'])->name('market.index');
Route::get('/market/{product:slug}', [MarketController::class, 'show'])->name('market.show');
Route::get('/brands/{brand:slug}', [MarketController::class, 'brand'])->name('market.brand');
// Buying (login needed)
Route::middleware('auth')->group(function () {
    Route::get('/checkout/{product:slug}', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout/{product:slug}', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/received', [OrderController::class, 'received'])->name('orders.received');
    Route::post('/orders/{order}/review', [\App\Http\Controllers\ReviewController::class, 'store'])->name('orders.review');
});

// Selling: orders for my brand
Route::middleware(['auth', EnsureUserHasBrand::class])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/ship', [SellerOrderController::class, 'ship'])->name('orders.ship');
});
Route::middleware('auth')->get('/me/role', function () {
    $brand = auth()->user()->brand;

    return response()->json(['brand' => $brand ? ['name' => $brand->name, 'slug' => $brand->slug] : null]);
})->name('me.role');

require __DIR__.'/auth.php';
