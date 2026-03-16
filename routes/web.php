<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('campaigns', 'pages::campaigns.index')->name('campaigns.index');
    Route::livewire('campaigns/create', 'pages::campaigns.create')->name('campaigns.create');
    Route::livewire('campaigns/{campaign}/link-character', 'pages::campaigns.link-character')->name('campaigns.link-character');
    Route::livewire('campaigns/{campaign}/edit', 'pages::campaigns.edit')->name('campaigns.edit');
    Route::livewire('campaigns/{campaign}/show', 'pages::campaigns.show')->name('campaigns.show');

    Route::livewire('characters', 'pages::characters.index')->name('characters.index');
    Route::livewire('characters/create', 'pages::characters.create')->name('characters.create');
    Route::livewire('characters/{character}/edit', 'pages::characters.edit')->name('characters.edit');
    Route::livewire('characters/{character}/show', 'pages::characters.show')->name('characters.show');

    Route::livewire('campaigns/{campaign}/play', 'pages::campaigns.play-campaign')->name('campaigns.play');

    Route::livewire('sandbox', 'pages::sandbox')->name('sandbox');
});

// Custom 2FA Route
Route::post('/two-factor-challenge', [\App\Http\Controllers\TwoFactorAuthenticatedSessionController::class, 'store'])
    ->middleware(['guest:'.config('fortify.guard')])
    ->name('two-factor.login.store');

Route::get('/docs/{page?}', [\App\Http\Controllers\DocsController::class, 'show'])->name('docs');

require __DIR__.'/settings.php';
