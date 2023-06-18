<?php

namespace App\Poster\SalesboxIntegration;

use App\Poster\meta\PosterCategory_meta;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\meta\CreatedSalesboxCategory_meta;
use App\Salesbox\meta\SalesboxApiResponse_meta;
use App\Salesbox\meta\SalesboxCategory_meta;
use App\Salesbox\meta\UpdatedSalesboxCategory_meta;

class SalesboxCategory
{
    /**
     * @param string|int $posterId
     * @param null|string|int $parentId
     * @param null|string|int $internalId
     * @return array
     */
    static public function getJsonForCreation($posterId): array {

        /** @var PosterCategory_meta $poster_category */
        $poster_category = collect(poster_fetchCategories())
            ->filter(poster_filterCategoriesById($posterId))
            ->first();

        /** @var SalesboxCategory_meta|null $salesbox_parentCategory */
        $salesbox_parentCategory = collect(salesbox_fetchCategories())
            ->filter(salesbox_filterCategoriesByExternalId($poster_category->parent_category))
            ->first();

        $json = [
            'available' => !!$poster_category->visible[0]->visible,
            'names' => [
                [
                    'name' => $poster_category->category_name,
                    'lang' => 'uk'
                ]
            ],
            'internalId' => $posterId,
            'externalId' => $posterId
        ];

        if($salesbox_parentCategory) {
            $json['parentId'] = $salesbox_parentCategory->internalId;
        } else if (!!$poster_category->parent_category) {
            $json['parentId'] = $poster_category->parent_category;
        }

        if (!empty($poster_category->category_photo_origin)) {
            $json['originalURL'] = config('poster.url') . $poster_category->category_photo_origin;;
        }

        if (!empty($poster_category->category_photo)) {
            $json['previewURL'] = config('poster.url') . $poster_category->category_photo;
        }

        return $json;
    }

    /**
     * @param string|int $posterId
     * @param null|string|int $parentId
     * @param null|string|int $internalId
     * @return array
     */
    static public function getJsonForUpdate($posterId): array {

        /** @var SalesboxCategory_meta $salesbox_category */
        $salesbox_category = collect(salesbox_fetchCategories())
            ->filter(salesbox_filterCategoriesByExternalId($posterId))
            ->first();
        /** @var PosterCategory_meta $poster_category */
        $poster_category = collect(poster_fetchCategories())
            ->filter(poster_filterCategoriesById($posterId))
            ->first();

        /** @var SalesboxCategory_meta $salesbox_parentCategory */
        $salesbox_parentCategory = collect(salesbox_fetchCategories())
            ->filter(salesbox_filterCategoriesByExternalId($poster_category->parent_category))
            ->first();


        $json = [
            'id' => $salesbox_category->id,
            'available' => !!$poster_category->visible[0]->visible,
            'internalId' => $posterId,
            'names' => [
                [
                    'name' => $poster_category->category_name,
                    'lang' => 'uk' // todo: should this language be configurable?
                ]
            ],
            'descriptions' => [],
            'photos' => [],
        ];

        if($salesbox_parentCategory) {
            $json['parentId'] = $salesbox_parentCategory->internalId;
        } else if(!!$poster_category->parent_category) {
            $json['parentId'] = $poster_category->parent_category;
        }

        // update photo only if it isn't already present
        if (!isset($salesbox_category->previewURL) && $poster_category->category_photo) {
            $json['previewURL'] = config('poster.url') . $poster_category->category_photo;
            $json['originalURL'] = config('poster.url') . $poster_category->category_photo_origin;
        }
        return $json;
    }
    /**
     * @param $posterId
     * @return CreatedSalesboxCategory_meta
     */
    static public function create($posterId, $parentPosterId = null)
    {
        $json = self::getJsonForCreation($posterId, $parentPosterId);

        $salesbox_createCategoryResponse = SalesboxApi::createCategory([
            'category' => $json
        ]);

        /** @var CreatedSalesboxCategory_meta $salesbox_category */
        $salesbox_category = (object)$salesbox_createCategoryResponse->data->ids[0];


        return $salesbox_category;
    }

    /**
     * @param $posterId
     * @return UpdatedSalesboxCategory_meta
     */
    static protected function update($posterId, $parentId = null)
    {
        $json = self::getJsonForUpdate($posterId, $parentId);

        $updateManyRes = SalesboxApi::updateCategory([
            'category' => $json
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
        SalesboxApi::authenticate(salesbox_fetchAccessToken()->token);

        $category = collect(salesbox_fetchCategories())
            ->filter(salesbox_filterCategoriesByExternalId($posterId))
            ->first();

        if (!$category) {
            // todo: should I throw exception if category doesn't exist?
            return null;
        }

        // recursively=true is important,
        // without this param salesbox will throw an error if the category being deleted has child categories
        return SalesboxApi::deleteCategory([
            'id' => $category->id,
            'recursively' => true
        ], []);
    }

    public static function createMany() {

    }

}
