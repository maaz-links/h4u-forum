<?php

namespace App\Providers;

use App\Models\UserConfig;
use Config;
use DB;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Log;
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
        
     Paginator::useBootstrap();
        JsonResource::withoutWrapping();
        Log::info("__________________________________________");
        // DB::listen(function ($query) {
        //     // Log the query and its execution time
        //     Log::info("Query: {$query->sql}, Time: {$query->time}ms");
        // });
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
