<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use App\Services\SettingsService;
use Config;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(SettingsService $settingsService)
    {
        Blade::component('modal', \App\View\Components\Modal::class);
        View::composer('layouts.app', function ($view) use ($settingsService) {
            $data = $settingsService->getdataById(1);
            $view->with('globalsettings', $data);
            
        });

        //check if configuration exist
        try {
            // Ensure the table exists before querying it
            if (Schema::hasTable('configurations')) {
                $settingsService = app()->make('App\Services\SettingsService'); // Ensure dependency injection
                $getConfiguration = $settingsService->getConfiguration();

                if ($getConfiguration > 0) {
                    $setting = $settingsService->getDataById(1);

                    // Set the timezone
                    Config::set('app.timezone', $setting['timezone']);
                    \Carbon\Carbon::setToStringFormat($setting['datetime']);

                    // Set default timezone
                    date_default_timezone_set(config('app.timezone'));
                }
            }
        } catch (Exception $e) {
            // Log the exception instead of var_dump
            logger()->error('Error setting configuration in AppServiceProvider: ' . $e->getMessage());
        }
        
       
    }
}
