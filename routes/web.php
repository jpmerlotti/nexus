<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Custom 2FA Route
Route::post('/two-factor-challenge', [\App\Http\Controllers\TwoFactorAuthenticatedSessionController::class, 'store'])
    ->middleware(['guest:'.config('fortify.guard')])
    ->name('two-factor.login.store');

Route::get('/docs/{page?}', [\App\Http\Controllers\DocsController::class, 'show'])->name('docs');

require __DIR__.'/settings.php';