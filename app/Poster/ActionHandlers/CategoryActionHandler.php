<?php

namespace App\Poster\ActionHandlers;

use App\Poster\meta\PosterCategory_meta;
use App\Poster\PosterApiWrapper;
use App\Poster\SalesboxApiWrapper;
use App\Poster\SalesboxIntegration\SalesboxCategory;
use App\Salesbox\Facades\SalesboxApi;

class CategoryActionHandler extends AbstractActionHandler
{
    public $pendingCategoryIdsForCreation = [];
    public $pendingCategoryIdsForUpdate = [];

    public function checkParent($posterId)
    {
        $salesbox_category = SalesboxApiWrapper::getCategory($posterId);
        $poster_category = PosterApiWrapper::getCategory($posterId);

        if (!$salesbox_category) {
            $this->pendingCategoryIdsForCreation[] = $posterId;
        }

        if (!!$poster_category->parent_category) {
            $this->checkParent($poster_category->parent_category);
        }
    }

    public function handle(): bool
    {
        if ($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            SalesboxApiWrapper::authenticate();

            $salesbox_categoryIds = SalesboxApiWrapper::getCategories()
                ->filter(function ($id) {
                    // todo: should I ignore all salesbox categories without external id?
                    // todo: or should I delete them as well in the synchronization process
                    return !empty($id);
                })
                ->pluck('externalId');

            $posterId = $this->getObjectId();

            $poster_category = PosterApiWrapper::getCategory($posterId);

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
                $categories = PosterApiWrapper::getCategories()
                    ->filter(
                    /** @param PosterCategory_meta $category */
                        function ($category) {
                            return in_array($category->category_id, $this->pendingCategoryIdsForCreation);
                        }
                    )->map(
                    /** @param PosterCategory_meta $category */
                        function ($category) {
                            $salesbox_parentCategory = SalesboxApiWrapper::getCategory($category->parent_category);
                            $parentId = $salesbox_parentCategory->internalId ?? $category->parent_category;

                            return SalesboxCategory::getJsonForCreation(
                                $category->category_id,
                                $parentId,
                                $category->category_id
                            );
                        })->values()->toArray();

                SalesboxApi::createManyCategories([
                    'categories' => $categories
                ]);
            }

            if (count($this->pendingCategoryIdsForUpdate) > 0) {
                $categories = PosterApiWrapper::getCategories()
                    ->filter(
                    /** @param PosterCategory_meta $category */
                        function ($category) {
                            return in_array($category->category_id, $this->pendingCategoryIdsForUpdate);
                        }
                    )->map(
                    /** @param PosterCategory_meta $category */
                        function ($category) {
                            $salesbox_parentCategory = SalesboxApiWrapper::getCategory($category->parent_category);
                            $parentId = $salesbox_parentCategory->internalId ?? $category->parent_category;
                            return SalesboxCategory::getJsonForUpdate(
                                $category->category_id,
                                $parentId,
                                $category->category_id
                            );
                        })->values()->toArray();

                SalesboxApi::updateManyCategories([
                    'categories' => $categories
                ]);
            }


        }

        if ($this->isRemoved()) {
            SalesboxCategory::delete($this->getObjectId());
        }

        return true;
    }


}
