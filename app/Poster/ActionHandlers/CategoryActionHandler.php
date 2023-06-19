<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Facades\PosterStore;
use App\Poster\Facades\SalesboxStore;
use App\Poster\PosterCategory;

class CategoryActionHandler extends AbstractActionHandler
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

            $update_ids = [];
            $create_ids = [];
            $salesbox_category = SalesboxStore::findCategory($this->getObjectId());
            $poster_category = PosterStore::findCategory($this->getObjectId());

            if($salesbox_category) {
                $update_ids[] = $this->getObjectId();
            } else {
                $create_ids[] = $this->getObjectId();
            }

            if($poster_category->hasParentCategory()) {
                $poster_parent_categories = $poster_category->getParents();

                foreach($poster_parent_categories as $parent_category) {
                    if(!SalesboxStore::categoryExists($parent_category->getCategoryId())) {
                        $create_ids[] = $parent_category->getCategoryId();
                    }
                }
            }

            // make updates
            if (count($create_ids) > 0) {
                $poster_categories_to_create = array_filter(PosterStore::getCategories(), function($poster_category) use($create_ids) {
                    return in_array($poster_category->getCategoryId(), $create_ids);
                });

                $poster_categories_as_salesbox_ones = array_map(function(PosterCategory $poster_category) {
                    return $poster_category
                        ->asSalesboxCategory();
                }, $poster_categories_to_create);

                SalesboxStore::createManyCategories($poster_categories_as_salesbox_ones);
            }

            if (count($update_ids) > 0) {
                $categories_to_update = array_filter(PosterStore::getCategories(), function($poster_category) use($update_ids) {
                    return in_array($poster_category->getCategoryId(), $update_ids);
                });
                $poster_categories_as_salesbox_ones = array_map(function(PosterCategory $poster_category) {
                    return $poster_category
                        ->asSalesboxCategory();
                }, $categories_to_update);

                SalesboxStore::updateManyCategories($poster_categories_as_salesbox_ones);
            }
        }

        if ($this->isRemoved()) {
            SalesboxStore::authenticate();
            SalesboxStore::loadCategories();

            $salesbox_category = SalesboxStore::findCategory($this->getObjectId());

            if (!$salesbox_category) {
                return false;
            }

            SalesboxStore::deleteCategory($this->getObjectId());
        }

        return true;
    }


}
