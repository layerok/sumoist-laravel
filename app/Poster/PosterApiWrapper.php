<?php

namespace App\Poster;

use App\Poster\meta\PosterApiResponse_meta;
use App\Poster\meta\PosterCategory_meta;
use App\Poster\meta\PosterProduct_meta;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use poster\src\PosterApi;

class PosterApiWrapper
{
    static public function getCategories(): Collection
    {
        /** @var PosterApiResponse_meta $poster_productsResponse */
        $poster_productsResponse = perRequestCache()
            ->rememberForever(CacheKeys::POSTER_CATEGORIES, function () {
                return PosterApi::menu()->getCategories();
            });

        Utils::assertResponse($poster_productsResponse, 'getCategories');

        return collect($poster_productsResponse->response);
    }

    /**
     * @param $posterId
     * @return PosterCategory_meta|null
     */
    static public function getCategory($posterId)
    {
        return self::getCategories()
            ->filter(
            /** @param PosterCategory_meta $category */
                function ($category) use ($posterId) {
                    return $category->category_id == $posterId;
                }
            )->first();
    }


    static public function categoryExists($posterId): bool
    {
        return !!self::getCategory($posterId);
    }

    static public function productExists($posterId): bool
    {
        return !!self::getProduct($posterId);
    }

    /**
     * @param array|int|string|null $posterIds
     * @return Collection
     */
    public static function getProducts($poster_ids = null): Collection
    {
        /** @var PosterApiResponse_meta $posterProductsResponse */
        $poster_productsResponse = perRequestCache()
            ->rememberForever('poster.products', function () {
                return PosterApi::menu()->getProducts();
            });

        Utils::assertResponse($poster_productsResponse, 'getProducts');

        $collection = collect($poster_productsResponse->response);

        if (!is_null($poster_ids)) {
            $poster_ids = Arr::wrap($poster_ids);
            $collection = $collection->filter(
            /**
             * @param PosterProduct_meta $product
             */
                function ($product) use ($poster_ids) {
                    return in_array($product->product_id, $poster_ids);
                }
            );
        }
        return $collection;
    }

    /**
     * @param $posterId
     * @return PosterProduct_meta|null
     */
    public static function getProduct($posterId)
    {
        return self::getProducts()->filter(/** @param $product PosterProduct_meta */
            function ($product) use ($posterId) {
                return $product->product_id === $posterId;
            })->first();
    }
}
