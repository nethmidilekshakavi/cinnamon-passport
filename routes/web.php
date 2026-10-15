<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\CollectorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\VerifyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

// Public batch verification (QR codes point here). No login needed.
Route::get('/verify', [VerifyController::class, 'index'])->name('verify.index');
Route::post('/verify', [VerifyController::class, 'lookup'])->name('verify.lookup');
Route::get('/verify/{code}', [VerifyController::class, 'show'])->name('verify.show');

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

    // SMS simulator: growers and collectors (role is checked in the controller).
    Route::get('/sms', [SmsController::class, 'index'])->name('sms.index');
    Route::post('/sms', [SmsController::class, 'store'])->name('sms.store');
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

require __DIR__.'/auth.php';
