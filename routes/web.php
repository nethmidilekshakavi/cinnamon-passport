<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

// After login, each role goes to its own home page.
Route::get('/dashboard', function (Request $request) {
    return match ($request->user()->role) {
        'collector' => redirect()->route('collector.home'),
        'buyer' => redirect()->route('buyer.home'),
        default => redirect()->route('batches.index'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:grower'])->group(function () {
    Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/create', [BatchController::class, 'create'])->name('batches.create');
    Route::post('/batches', [BatchController::class, 'store'])->name('batches.store');
});

Route::middleware(['auth', 'role:collector'])->get('/collector', function () {
    return Inertia::render('ComingSoon', [
        'titleKey' => 'nav.collectorArea',
        'messageKey' => 'soon.collector',
    ]);
})->name('collector.home');

Route::middleware(['auth', 'role:buyer'])->get('/buyer', function () {
    return Inertia::render('ComingSoon', [
        'titleKey' => 'nav.buyerArea',
        'messageKey' => 'soon.buyer',
    ]);
})->name('buyer.home');

Route::get('/sms', [SmsController::class, 'index'])->name('sms.index');
Route::post('/sms', [SmsController::class, 'store'])->name('sms.store');

require __DIR__.'/auth.php';
