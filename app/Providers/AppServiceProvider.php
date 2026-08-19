<?php

namespace App\Providers;

use App\Contracts\ActivityLoggerInterface;
use App\Contracts\GroupCodeGeneratorInterface;
use App\Services\ActivityLogger;
use App\Services\GroupCodeGeneratorService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ActivityLoggerInterface::class, ActivityLogger::class);
        $this->app->bind(GroupCodeGeneratorInterface::class, GroupCodeGeneratorService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
