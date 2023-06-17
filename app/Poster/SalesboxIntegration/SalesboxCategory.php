<?php

namespace App\Poster\SalesboxIntegration;

use App\Poster\CacheKeys;
use App\Poster\meta\PosterApiResponse_meta;
use App\Poster\meta\PosterCategory_meta;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\meta\CreatedSalesboxCategory_meta;
use App\Salesbox\meta\SalesboxApiResponse_meta;
use App\Salesbox\meta\SalesboxCategory_meta;
use App\Salesbox\meta\UpdatedSalesboxCategory_meta;
use Illuminate\Support\Collection;
use poster\src\PosterApi;

class SalesboxCategory
{
    static protected function authenticate()
    {
        /** @var SalesboxApiResponse_meta $salesbox_accessTokenResponse */
        $salesbox_accessTokenResponse = perRequestCache()
            ->rememberForever(CacheKeys::SALESBOX_ACCESS_TOKEN, function () {
                return SalesboxApi::getAccessToken();
            });

        SalesboxApi::authenticate($salesbox_accessTokenResponse->data->token);
    }

    static public function salesbox_getCategories(): Collection
    {
        self::authenticate();
        /** @var SalesboxApiResponse_meta $salesbox_categoriesResponse */
        $salesbox_categoriesResponse = perRequestCache()
            ->rememberForever(CacheKeys::SALESBOX_CATEGORIES, function () {
                return SalesboxApi::getCategories();
            });

        return collect($salesbox_categoriesResponse->data);
    }

    static public function poster_getCategories(): Collection
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
    static public function poster_getCategory($posterId) {
        return self::poster_getCategories()
            ->filter(
            /** @param PosterCategory_meta $category */
                function ($category) use ($posterId) {
                    return $category->category_id == $posterId;
                }
            )->first();
    }

    /**
     * @param $posterId
     * @return SalesboxCategory_meta | null
     */
    static public function salesbox_getCategory($externalId) {
        return self::salesbox_getCategories()->filter(
        /** @param SalesboxCategory_meta $category */
            function ($category) use ($externalId) {
                return $category->externalId === $externalId;
            })->first();
    }

    /**
     * @param $posterId
     * @return CreatedSalesboxCategory_meta|UpdatedSalesboxCategory_meta
     */
    static public function sync($posterId, $syncParents = true)
    {
        self::authenticate();
        $salesbox_category = self::salesbox_getCategory($posterId);

        if (!$salesbox_category) {
            return SalesboxCategory::create($posterId, $syncParents);
        }

        return SalesboxCategory::update($posterId, $salesbox_category, $syncParents);
    }

    /**
     * @param $posterId
     * @return CreatedSalesboxCategory_meta
     */
    static protected function create($posterId, $syncParents = true)
    {
        $poster_category = self::poster_getCategory($posterId);

        $salesbox_newCategory = [
            'available' => !!$poster_category->visible[0]->visible,
            'names' => [
                [
                    'name' => $poster_category->category_name,
                    'lang' => 'uk'
                ]
            ],
            'externalId' => $posterId
        ];

        if (!empty($poster_category->category_photo_origin)) {
            $salesbox_newCategory['originalURL'] = config('poster.url') . $poster_category->category_photo_origin;;
        }

        if(!empty($poster_category->category_photo)) {
            $salesbox_newCategory['previewURL'] = config('poster.url') . $poster_category->category_photo;
        }

        if (!!$poster_category->parent_category && $syncParents) {
            $salesbox_parentCategory = self::sync($poster_category->parent_category);
            $salesbox_newCategory['parentId'] = $salesbox_parentCategory->internalId;
        }

        $salesbox_createCategoryResponse = SalesboxApi::createCategory([
            'category' => $salesbox_newCategory
        ]);

        /** @var CreatedSalesboxCategory_meta $salesbox_category */
        $salesbox_category = (object)$salesbox_createCategoryResponse->data->ids[0];

        return $salesbox_category;
    }

    /**
     * @param $posterId
     * @param SalesboxCategory_meta $salesbox_category
     * @return UpdatedSalesboxCategory_meta
     */
    static protected function update($posterId, $salesbox_category, $syncParents)
    {
        /** @var PosterCategory_meta $poster_category */
        $poster_category = self::poster_getCategory($posterId);

        $salesbox_changedCategory = [
            'id' => $salesbox_category->id,
            'available' => !!$poster_category->visible[0]->visible,
            'names' => [
                [
                    'name' => $poster_category->category_name,
                    'lang' => 'uk' // todo: should this language be configurable?
                ]
            ],
        ];

        if (!!$poster_category->parent_category && $syncParents) {
            $salesbox_parentCategory = self::sync($poster_category->parent_category);
            $salesbox_changedCategory['parentId'] = $salesbox_parentCategory->internalId;
        }

        // update photo only if it isn't already present
        if (!isset($salesbox_category->previewURL) && $poster_category->category_photo) {
            $salesbox_changedCategory['previewURL'] = config('poster.url') . $poster_category->category_photo;
            $salesbox_changedCategory['originalURL'] = config('poster.url') . $poster_category->category_photo_origin;
        }


        $updateManyRes = SalesboxApi::updateCategory([
            'category' => $salesbox_changedCategory
        ]);

        /**
         * @var UpdatedSalesboxCategory_meta $salesbox_updatedCategory
         */
        $salesbox_updatedCategory = $updateManyRes->data->ids[0];

        return $salesbox_updatedCategory;
    }

    /**
     * @param $posterId
     * @return SalesboxApiResponse_meta|null
     */
    public static function delete($posterId)
    {
        self::authenticate();

        $category = self::salesbox_getCategory($posterId);

        if (!$category) {
            // todo: should I throw exception if category doesn't exist?
            return null;
        }

        // recursively=true is important,
        // without this param salesbox will throw an error if the category being deleted has child categories
        return SalesboxApi::deleteCategory([
            'id' => $category['id'],
            'recursively' => true
        ], []);
    }

}
