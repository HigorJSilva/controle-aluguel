<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('/dashboard', 'pages.dashboard.index')->name('dashboard');

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
        Volt::route('/{imovel}/edit', 'pages.imoveis.edit')->name('edit');
        Volt::route('/{imovel}', 'pages.imoveis.show')->name('show');
    });

    Route::prefix('inquilinos')->name('inquilinos.')->group(function () {
        Volt::route('/', 'pages.inquilinos.index')->name('index');
        Volt::route('/create', 'pages.inquilinos.create')->name('create');
        Volt::route('/{inquilino}/edit', 'pages.inquilinos.edit')->name('edit');
        Volt::route('/{inquilino}', 'pages.inquilinos.show')->name('show');
    });

    Route::prefix('locacoes')->name('locacoes.')->group(function () {
        Volt::route('/', 'pages.locacoes.index')->name('index');
        Volt::route('/create', 'pages.locacoes.create')->name('create');
        Volt::route('/{locacao}/edit', 'pages.locacoes.edit')->name('edit');
        Volt::route('/{locacao}', 'pages.locacoes.show')->name('show');
    });

    Route::prefix('pagamentos')->name('pagamentos.')->group(function () {
        Volt::route('/', 'pages.pagamentos.index')->name('index');
        Volt::route('/create', 'pages.pagamentos.create')->name('create');
        Volt::route('/{pagamento}/edit', 'pages.pagamentos.edit')->name('edit');
        Volt::route('/{pagamento}', 'pages.pagamentos.show')->name('show');
    });
});

require __DIR__ . '/auth.php';
