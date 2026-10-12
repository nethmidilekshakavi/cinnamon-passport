<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BatchController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:grower'])->get('/grower', fn () => 'Grower area');
Route::middleware(['auth', 'role:collector'])->get('/collector', fn () => 'Collector area');
Route::middleware(['auth', 'role:buyer'])->get('/buyer', fn () => 'Buyer area');

Route::middleware(['auth', 'role:grower'])->group(function () {
    Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/create', [BatchController::class, 'create'])->name('batches.create');
    Route::post('/batches', [BatchController::class, 'store'])->name('batches.store');
});

require __DIR__.'/auth.php';
