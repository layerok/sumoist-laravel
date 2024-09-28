<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Poster\Facades\PosterStore;
use App\Salesbox\Facades\SalesboxStore;
use Illuminate\Support\Str;

class SalesboxMigrationController {
    public function __invoke() {
        SalesboxStore::authenticate();
        PosterStore::init();
        $categories = SalesboxStore::loadCategories();
        $offers = SalesboxStore::loadOffers();
        $posterProducts  = PosterStore::loadProducts();

        foreach ($categories as $salesboxCategory) {
            $exist = Category::where('description', $salesboxCategory->getId())->first();
            if($exist) {
                $exist->delete();
            }
            if($salesboxCategory->getAvailable()) {
                $siteCategory = new Category();
                $siteCategory->name = $salesboxCategory->getName();
                $siteCategory->hidden = 0;
                $siteCategory->sort_order = $salesboxCategory->getSort() ?? 0;
                $siteCategory->slug = Str::slug($salesboxCategory->getName());
                $siteCategory->description = $salesboxCategory->getId();
                $siteCategory->save();
            }
        }

        foreach($offers as $offer) {
            if($offer->getAvailable()) {
                $posterProduct = PosterStore::findProduct($offer->getExternalId());

                 if(!$posterProduct->hasProductModifications()) {
                    $exist = Product::where('poster_id', $offer->getExternalId())->first();
                    if($exist) {
                        $exist->delete();
                    }

                    $product = new Product();
                    $product->name = $offer->getName();
                    $product->slug = Str::slug($offer->getName());
                    $product->image = $offer->getOriginalURL();
                    $product->price = $offer->getPrice();
                    $product->poster_id = $offer->getExternalId();
                    $product->hidden = 0;
                    $product->weight = $posterProduct->getOut();
                    $product->description = $offer->getDescription();
                    $ids = [];
                    foreach($offer->getCategories() as $category) {
                        $siteCategory = Category::where('description', $category['id'])->first();
                        if($siteCategory) {
                            $ids[] = $siteCategory->id;
                        }
                    }
                    $product->save();
                    $product->categories()->sync($ids);

                }

                //$product->save();
            }
        }

        foreach ($posterProducts as $posterProduct) {

            if($posterProduct->hasProductModifications()) {
                $offer = SalesboxStore::findOfferByExternalId($posterProduct->getProductId());

                $product = Product::where('poster_id', $offer->getExternalId())->first();

                if($product) {
                    foreach($product->attributes as $attribute) {
                        $attribute->attributeValues()->detach();
                    }
                    $product->attributes()->delete();
                }

                $product = new Product();
                $product->name = $posterProduct->getProductName();
                $product->slug = Str::slug($posterProduct->getProductName());
                $product->image = $offer->getOriginalURL();
                $product->price = $offer->getPrice();
                $product->poster_id = $offer->getExternalId();
                $product->hidden = 0;
                $product->weight = $posterProduct->getOut();
                $product->description = $offer->getDescription();
                $ids = [];
                foreach($offer->getCategories() as $category) {
                    $siteCategory = Category::where('description', $category['id'])->first();
                    if($siteCategory) {
                        $ids[] = $siteCategory->id;
                    }
                }
                $product->save();
                $product->categories()->sync($ids);


                $attribute = Attribute::where('code', str_slug($posterProduct->getProductName()). "_modificator")->first();
                if($attribute) {
                    $attribute->delete();
                }
                $attribute_id = Attribute::insertGetId([
                    'name'          => $posterProduct->getProductName()  . " modificator attribute",
                    'code'          => str_slug($posterProduct->getProductName()). "_modificator" ,
                    'frontend_type' => 'radio',
                    'is_filterable' => 1
                ]);


                foreach($posterProduct->getProductModifications() as $modification) {
                    $attributeValue = AttributeValue::where('poster_id', $modification->getModificatorId())->first();
                    if($attributeValue) {
                        $attributeValue->delete();
                    }
                    $attribute_value_id = AttributeValue::insertGetId([
                        'attribute_id' => $attribute_id,
                        'value'        => $modification->getModificatorName(),
                        'poster_id'    => $modification->getModificatorId(),
                        'price'        => $modification->getFirstPrice()
                    ]);

                    $product_attribute = ProductAttribute::create([
                        'price'         => $modification->getFirstPrice(),
                        'product_id'    => $product->id
                    ]);

                    $product_attribute->attributeValues()->attach($attribute_value_id);
                }





            }

        }

    }
}
