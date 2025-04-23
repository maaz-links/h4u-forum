<?php

namespace App\Providers;

use App\Models\UserConfig;
use Config;
use Illuminate\Support\ServiceProvider;

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
        $allConfigs = UserConfig::get();
        foreach ($allConfigs as $c) {
            Config::set($c->key, $c->value);
        }
        //
    }
}
