<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Slider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Cart;
use Theme;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        View::composer('theme::site.partials.nav', function ($view) {
            $view->with('categories', Category::where([
                ['id', '>' , 1],
                ['hidden', '=' , 0],
            ])->orderBy('sort_order', 'asc')->get());
        });
        View::composer('theme::site.partials.header', function ($view) {
            $view->with('cartCount', Cart::getContent()->count());
        });
        View::composer('theme::site.partials.header', function ($view) {
            $view->with('menu', Page::where('hidden', '=', '0')->get());
        });

        View::composer('theme::site.partials.slider', function ($view) {
            $view->with('slides', Slider::where('hidden', '=', '0')->get());
        });
    }
}
