<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (!\App::runningInConsole() && count(Schema::getColumnListing('settings'))) {
            $theme = Setting::where('key', '=', 'theme')->value('value');
        }



        if(!isset($theme)){
            $theme = env('APP_THEME', 'default');
        }

        $views = [
            resource_path("views/themes/{$theme}"),
            resource_path("views/themes/default")
        ];

        $this->loadViewsFrom($views, 'theme');
    }
}
