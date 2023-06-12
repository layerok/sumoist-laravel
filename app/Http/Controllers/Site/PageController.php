<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Models\Page;
use Illuminate\Support\Facades\Storage;
use Cart;

class PageController extends Controller
{
    public function index(Page $page){

        $theme = config('settings.theme');

        $files = Storage::disk('views')->files($theme. '/site/pages');

        if($page->slug == 'thankyou'){
            Cart::clear();
        }

        if(in_array($theme.'/site/pages/' . $page->slug .'.blade.php', $files)){
            return view('theme::site.pages.' .$page->slug, compact('page') );
        }else{
            return view('theme::site.pages.default', compact('page'));
        }

    }
}
