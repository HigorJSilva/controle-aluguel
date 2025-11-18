<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\SocialiteProviders;
use App\Services\CEP\BuscaCepStrategy;
use App\Services\CEP\ViaCepStrategy;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('provider', fn (string $value) => SocialiteProviders::from($value)->make());

        $this->app->bind(BuscaCepStrategy::class, ViaCepStrategy::class);

        // Adicione esta condição para forçar o HTTP em ambientes que não sejam de produção
        if (config('app.env') !== 'production') {
            URL::forceScheme('http');
        }
    }
}
