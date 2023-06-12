<?php
namespace App\Libraries;


use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Storage;

class Asset {

    public static function load($name, $admin = false){
        if($admin){
            $path = "themes/admin/{$name}";
        }else{
            $theme = Setting::where('key', '=', 'theme')->first();
            $path = "themes/{$theme->value}/assets/{$name}";
        }


        $exists = Storage::disk('public')->exists($path);
        if($exists){
            $hash = Storage::disk('public')->lastModified($path);
            return asset("/storage/" . $path . '?' . $hash );
        }else{
            return "/storage/" . $path;
        }

    }

}
