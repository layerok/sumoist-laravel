<?php
namespace App\Libraries;


use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class Image {

    public static function getPath($product){

        $path = '/storage/' . 'img/default-thumb.png';

        if(isset($product->associatedModel->attributes)  && $product->associatedModel->exists()){
            $product = $product->associatedModel;

        }


        if(isset($product)){
            if( count($product->images) > 0) {

                $path = '/storage/' . $product->images()->first()->full;

            }else{
                if (isset($product->image)) {
                    $path = $product->image;
                }
            }
        }


        return $path;
    }



}
