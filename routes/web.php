<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');

    // Route::prefix('users')->name('users.')->group(function () {
    //     Volt::route('/', 'pages.users.index')->name('index');
    //     Volt::route('/create', 'pages.users.create')->name('create');
    //     Volt::route('/{user}/edit', 'pages.users.edit')->name('edit');
    //     // Add more user routes here as needed
    // });

    Route::prefix('imoveis')->name('imoveis.')->group(function () {
        Volt::route('/', 'pages.imoveis.index')->name('index');
        Volt::route('/create', 'pages.imoveis.create')->name('create');
        Volt::route('/{user}/edit', 'pages.imoveis.edit')->name('edit');
    });
});

require __DIR__ . '/auth.php';
