<?php

namespace App\Providers;

use App\Domain\Services\RoomAvailabilityServiceInterface;
use App\Services\RoomAvailabilityService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    public function boot(): void
    {
        $this->app->bind(RoomAvailabilityServiceInterface::class, RoomAvailabilityService::class);
    }
}
