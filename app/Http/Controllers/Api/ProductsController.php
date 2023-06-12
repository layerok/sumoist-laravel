<?php

namespace App\Http\Controllers\Api;


use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Libraries\Poster;

class ProductsController extends Controller
{

    public function index($spotSlug)
    {
        $spot = DB::table('spots')->where('slug', $spotSlug)->first();

        $products = DB::table('products')->where('spot_id', $spot->id)->orderBy('poster_id','desc')->get();



        foreach($products as $key => $value) {

            $product = Product::with('ingredients')->where('id', $value->id)->first();

            $filtered = $product['ingredients']->filter(function ($value, $key) {
                return $value['status'] == 1;
            });

            //dd($ingredients['ingredients']);
            $ingredients_string = $filtered->implode( 'name' , ', ');
            $products[$key]->ingredients = $ingredients_string;

            $image = DB::table('product_images')->where('product_id', $value->id)->value('full');
            $products[$key]->image = $image;
            if($products[$key]->status == 0) {
                unset($products[$key]);
            }
        }
        return $products;
    }

    public function images(){
        $products = Product::with('images')->get();

        $result = [];
        foreach($products as $product) {
            $ingredients = [];
            foreach ($product->attributes as $productAttribute) {


                foreach ($productAttribute->attributeValues as $key => $attributeValue) {

                    if ($attributeValue->attribute_id == 3){

                        $ingredients[] = $attributeValue->value;
                    }

                }

            }

            $additional_photo = $product->images()->first()['full'];
            $img = $additional_photo ? 'https://emojisushi.com.ua/storage/' . $additional_photo : null ;
            $result[] = [
                'poster_id' => $product['poster_id'],
                'name' => $product['name'],
                'ingredients' => implode(', ', $ingredients),
                'image'     =>  $img,
            ];
        }
        echo json_encode($result);
    }

    public function getImage($posterId){
        $product = Product::with('images')->where('poster_id', $posterId)->first();
    }



}
