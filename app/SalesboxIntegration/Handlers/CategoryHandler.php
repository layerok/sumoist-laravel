<?php

namespace App\SalesboxIntegration\Handlers;

use App\Poster\Facades\PosterStore;
use App\Poster\Models\PosterCategory;
use App\Salesbox\Facades\SalesboxStore;
use App\Salesbox\Models\SalesboxCategory;
use App\SalesboxIntegration\Transformers\PosterCategoryAsSalesboxCategory;

class CategoryHandler extends AbstractHandler
{
    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function handle(): bool
    {
        if ($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            SalesboxStore::authenticate();
            PosterStore::loadCategories();
            SalesboxStore::loadCategories();

            $poster_category = PosterStore::findCategory($this->getObjectId());

            if (!PosterStore::categoryExists($this->getObjectId())) {
                throw new \RuntimeException(sprintf('category#%s not found in poster', $this->getObjectId()));
            }

            $update_ids = [];
            $create_ids = [];

            if (SalesboxStore::categoryExistsWithExternalId($this->getObjectId())) {
                $update_ids[] = $this->getObjectId();
            } else {
                $create_ids[] = $this->getObjectId();
            }

            if ($poster_category->hasParentCategory()) {
                $poster_parent_categories = PosterStore::getCategoryParents($poster_category);

                foreach ($poster_parent_categories as $parent_category) {
                    if (!SalesboxStore::categoryExistsWithExternalId($parent_category->getCategoryId())) {
                        $create_ids[] = $parent_category->getCategoryId();
                    }
                }
            }

            // make updates
            if (count($create_ids) > 0) {
                $poster_categories_as_salesbox_ones = array_map(function(PosterCategory $posterCategory) {
                    $transformer = new PosterCategoryAsSalesboxCategory($posterCategory);
                    return $transformer->transform();
                }, PosterStore::findCategory($create_ids));

                SalesboxStore::createManyCategories($poster_categories_as_salesbox_ones);
            }

            if (count($update_ids) > 0) {
                $poster_categories_as_salesbox_ones = array_map(function(PosterCategory $posterCategory) {
                    $salesbox_category = SalesboxStore::findCategoryByExternalId($posterCategory->getCategoryId());
                    $transformer = new PosterCategoryAsSalesboxCategory($posterCategory);
                    return $transformer->updateFrom($salesbox_category);
                }, PosterStore::findCategory($update_ids));

                array_map(function (SalesboxCategory $salesbox_category) {
                    // don't override photo if it is already present
                    if ($salesbox_category->getOriginalAttributes('previewURL')) {
                        $salesbox_category->resetAttributeToOriginalOne('previewURL');
                    }

                    if ($salesbox_category->getOriginalAttributes('originalURL')) {
                        $salesbox_category->resetAttributeToOriginalOne('originalURL');
                    }

                    // the same applies to 'names'
                    if (count($salesbox_category->getOriginalAttributes('names')) > 0) {
                        $salesbox_category->resetAttributeToOriginalOne('names');
                    }
                }, $poster_categories_as_salesbox_ones);

                SalesboxStore::updateManyCategories($poster_categories_as_salesbox_ones);
            }
        }

        return true;
    }







}
