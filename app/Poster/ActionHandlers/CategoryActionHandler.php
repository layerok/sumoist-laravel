<?php

namespace App\Poster\ActionHandlers;

use App\Poster\meta\PosterCategory_meta;
use App\Poster\SalesboxIntegration\SalesboxCategory;
use App\Salesbox\Facades\SalesboxApi;

class CategoryActionHandler extends AbstractActionHandler
{
    public $pendingCategoryIdsForCreation = [];
    public $pendingCategoryIdsForUpdate = [];

    public function checkParent($posterId)
    {
        $salesbox_category = collect(salesbox_fetchCategories())
            ->filter(salesbox_filterCategoriesByExternalId($posterId))
            ->first();
        $poster_category = collect(poster_fetchCategories())
            ->filter(poster_filterCategoriesById($posterId))
            ->first();

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
            SalesboxApi::authenticate(salesbox_fetchAccessToken()->token);

            $salesbox_categoryIds = collect(salesbox_fetchCategories())
                ->filter(function ($id) {
                    // todo: should I ignore all salesbox categories without external id?
                    // todo: or should I delete them as well in the synchronization process
                    return !empty($id);
                })
                ->pluck('externalId');

            $posterId = $this->getObjectId();

            $poster_category = collect(salesbox_fetchCategories())
                ->filter(poster_filterCategoriesById($posterId))
                ->first();

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
                function mapCategoryToCreateJson(): \Closure
                {
                    /** @param PosterCategory_meta $category */
                    return function ($category) {
                        $salesbox_parentCategory = collect(salesbox_fetchCategories())
                            ->filter(salesbox_filterCategoriesByExternalId($category->parent_category))
                            ->first();
                        $parentId = $salesbox_parentCategory->internalId ?? $category->parent_category;

                        return SalesboxCategory::getJsonForCreation(
                            $category->category_id,
                            $parentId,
                            $category->category_id
                        );
                    };
                }

                $categories = collect(poster_fetchCategories())
                    ->filter(poster_filterCategoriesById($this->pendingCategoryIdsForCreation))
                    ->map(mapCategoryToCreateJson())
                    ->values()
                    ->toArray();

                SalesboxApi::createManyCategories([
                    'categories' => $categories
                ]);
            }

            if (count($this->pendingCategoryIdsForUpdate) > 0) {
                function mapCategoryToUpdateJson(): \Closure
                {
                    /** @param PosterCategory_meta $category */
                    return function ($category) {
                        $salesbox_parentCategory = collect(salesbox_fetchCategories())
                            ->filter(salesbox_filterCategoriesByExternalId($category->parent_category))
                            ->first();
                        $parentId = $salesbox_parentCategory->internalId ?? $category->parent_category;
                        return SalesboxCategory::getJsonForUpdate(
                            $category->category_id,
                            $parentId,
                            $category->category_id
                        );
                    };
                }

                $categories = collect(poster_fetchCategories())
                    ->filter(poster_filterCategoriesById($this->pendingCategoryIdsForUpdate))
                    ->map(mapCategoryToUpdateJson())
                    ->values()
                    ->toArray();

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
