<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Staff;
use App\Models\User;
use App\Observers\StaffObserver;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
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
        // Register model observers for automatic email syncing
        Staff::observe(StaffObserver::class);
        User::observe(UserObserver::class);
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\SyncStaffUsers::class,
            ]);
        }
    }
}
