<?php

namespace App\Poster\ActionHandlers;

use App\Salesbox\Facades\SalesboxApi;

class CategoryActionHandler extends AbstractActionHandler
{
    public $pendingCategoryIdsForCreation = [];
    public $pendingCategoryIdsForUpdate = [];

    public function handle(): bool
    {
        if ($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            SalesboxApi::authenticate(salesbox_fetchAccessToken()->token);

            $salesbox_categoryIds = collect(salesbox_fetchCategories())
                ->filter(function ($id) {
                    // todo: should I ignore all salesbox categories without external id?
                    // todo: or should I delete them as well in the synchronization process
                    return !empty($id);
                })
                ->pluck('externalId');

            $posterId = $this->getObjectId();

            $poster_category = poster_fetchCategory($posterId);

            if ($salesbox_categoryIds->contains($posterId) && !in_array($posterId, $this->pendingCategoryIdsForUpdate)) {
                $this->pendingCategoryIdsForUpdate[] = $posterId;
            }

            if (!$salesbox_categoryIds->contains($posterId) && !in_array($posterId, $this->pendingCategoryIdsForCreation)) {
                $this->pendingCategoryIdsForCreation[] = $posterId;
            }

            if (!!$poster_category->parent_category) {
                $this->checkParent($poster_category->parent_category);
            }

            // make updates
            if (count($this->pendingCategoryIdsForCreation) > 0) {
                $categories = collect(poster_fetchCategories())
                    ->filter(poster_filterCategoriesByCategoryId($this->pendingCategoryIdsForCreation))
                    ->map('poster_mapCategoryToJson')
                    ->map(function($json) {
                        return collect($json)->only([
                            'internalId',
                            'externalId',
                            'parentId',
                            'previewURL',
                            'originalURL',
                            'names',
                            'available',
                            'photos',
                            'descriptions'
                        ]);
                    })
                    ->values()// array must be property indexed, otherwise salesbox api will fail
                    ->toArray();

                SalesboxApi::createManyCategories([
                    'categories' => $categories
                ]);
            }

            if (count($this->pendingCategoryIdsForUpdate) > 0) {

                $categories = collect(poster_fetchCategories())
                    ->filter(poster_filterCategoriesByCategoryId($this->pendingCategoryIdsForUpdate))
                    ->map('poster_mapCategoryToJson')
                    ->map(function($json) {
                        return collect($json)->only([
                            'id',
                            'internalId',
                            'externalId',
                            'previewURL',
                            'originalURL',
                            'parentId',
                            'names',
                            //'descriptions',
                            'photos',
                            'available'
                        ]);
                    })
                    ->map(function($json) {
                        // don't update photo if it is already present in salesbox
                        if(salesbox_categoryHasPhoto($json['externalId'])) {
                            unset($json['previewURL']);
                            unset($json['originalURL']);
                        }
                        return $json;
                    })
                    ->values() // array must be property indexed, otherwise salesbox api will fail
                    ->toArray();

                SalesboxApi::updateManyCategories([
                    'categories' => $categories
                ]);
            }


        }

        if ($this->isRemoved()) {
            SalesboxApi::authenticate(salesbox_fetchAccessToken()->token);

            $salesbox_category = salesbox_fetchCategory($posterId);

            if (!$salesbox_category) {
                // todo: should I throw exception if category doesn't exist?
                return false;
            }

            // recursively=true is important,
            // without this param salesbox will throw an error if the category being deleted has child categories
            SalesboxApi::deleteCategory([
                'id' => $salesbox_category->id,
                'recursively' => true
            ], []);
        }

        return true;
    }

    public function checkParent($posterId)
    {
        $salesbox_category = salesbox_fetchCategory($posterId);
        $poster_category = poster_fetchCategory($posterId);

        if (!$salesbox_category) {
            $this->pendingCategoryIdsForCreation[] = $posterId;
        }

        if (!!$poster_category->parent_category) {
            $this->checkParent($poster_category->parent_category);
        }
    }


}
