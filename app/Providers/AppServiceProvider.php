<?php

namespace App\Providers;

use App\Domain\Services\RoomAvailabilityServiceInterface;
use App\Services\RoomAvailabilityService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(RoomAvailabilityServiceInterface::class, RoomAvailabilityService::class);
    }
}
