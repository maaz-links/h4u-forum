<?php

namespace App\Providers;

use App\Models\UserConfig;
use Config;
use Illuminate\Support\ServiceProvider;
use Schema;

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
        // Check if the table exists before trying to query it
        if (Schema::hasTable('user_configs')) {
            $allConfigs = UserConfig::get();
            foreach ($allConfigs as $c) {
                Config::set($c->key, $c->value);
            }
        }
        //
    }
}
