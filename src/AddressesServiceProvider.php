<?php

declare(strict_types=1);

namespace AndyDefer\LaravelAddresses;

use AndyDefer\LaravelAddresses\Repositories\AddressRepository;
use AndyDefer\LaravelAddresses\Services\AddressService;
use Illuminate\Support\ServiceProvider;

final class AddressesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AddressRepository::class);
        $this->app->singleton(AddressService::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'Addresses-migrations');
    }
}
