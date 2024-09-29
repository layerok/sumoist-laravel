<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use poster\src\PosterApi;

class PosterWebhookController
{
    public function __invoke(Request $request)
    {
        try {
            PosterApi::init(config('poster_app_3666'));

            if (!PosterApi::auth()->verifyWebHook($request->getContent())) {
                Log::error("Request signatures didn't match!" . $request->getContent());
                return response("error", 200);
            }

            $postData = $request->post();

            switch ($postData['action']) {
                case "added":
                    $this->createProduct($postData);
                    break;
                case "changed":
                    $this->updateProduct($postData);
                    break;
                case "removed":
                    $this->deleteProduct($postData);
                    break;
            }

        } catch (\Throwable $exception) {
            Log::error($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
            return response('error', 200);
        } finally {
            return response('ok');
        }
    }


    public function createProduct($postData) {
        $result = (object) PosterApi::menu()->getProduct([
            'product_id' => $postData['object_id']
        ]);

        $posterProduct = $result->response;

        if (!$posterProduct) {
            // we haven't found this product in poster
            return;
        }

        if(isset($posterProduct->modifications)) {
            // we don't support modifications
            return;
        }

        $siteProduct = Product::where("poster_id", "=", $posterProduct->product_id)->first();

        if($siteProduct) {
            // product has been already created on site
            $this->updateProduct($postData);
            return;
        }

        function delEndind(string $str){
            return !empty($str) ? substr($str, 0, strlen($str)-2): 0;
        }

        $price = delEndind($posterProduct->price->{"1"});

        $description = collect($posterProduct->ingredients ?? [])->map(function($ingredient) {
            return $ingredient->ingredient_name;
        })->join(', ');

        $image = $posterProduct->photo ? config('poster.url') . $posterProduct->photo : null;

        $data = [
            'name'          => $posterProduct->product_name,
            'weight'        => $posterProduct->out,
            'image'         => $image,
            'unit'          => 'г',
            'sort_order'    => $posterProduct->sort_order,
            'poster_id'     => $posterProduct->product_id,
            'price'         => $price,
            'description'   => $description,
            'hidden'        => true
        ];

        Product::create($data);
    }

    public function updateProduct($postData)
    {
        $result = (object)PosterApi::menu()->getProduct([
            'product_id' => $postData['object_id']
        ]);

        $posterProduct = $result->response;

        if (!$posterProduct) {
            // we haven't found this product in poster
            return;
        }

        if(isset($value->modifications)) {
            // we don't support modifications
            return;
        }

        $siteProduct = Product::where("poster_id", "=", $posterProduct->product_id)->first();

        if(!$siteProduct) {
            $this->createProduct($postData);
            return;
        }

        function delEndind(string $str){
            return !empty($str) ? substr($str, 0, strlen($str)-2): 0;
        }

        $data = [
            'price' => delEndind($posterProduct->price->{"1"}),
            'weight' => $posterProduct->out,
//                    'hidden' => $product['spots'][0]['visible'] == 1 ? 0 : 1
        ];

        $siteProduct->update($data);
    }

    public function deleteProduct($postData)
    {
        $siteProduct = Product::where("poster_id", "=", $postData['object_id'])->first();

        if(!$siteProduct) {
            // nothing to delete
            return;
        }

        $siteProduct->delete();
    }

}
