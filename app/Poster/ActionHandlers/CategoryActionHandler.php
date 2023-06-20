<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Facades\PosterStore;
use App\Poster\Facades\SalesboxStore;
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

            $poster_category = PosterStore::findCategory($this->getObjectId());

            if(!PosterStore::categoryExists($this->getObjectId())){
                throw new \RuntimeException(sprintf('category#%s not found in poster', $this->getObjectId()));
            }

            $update_ids = [];
            $create_ids = [];

            if(SalesboxStore::categoryExists($this->getObjectId())) {
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
                $poster_categories_as_salesbox_ones = PosterStore::asSalesboxCategories(
                    PosterStore::findCategory($create_ids)
                );

                SalesboxStore::createManyCategories($poster_categories_as_salesbox_ones);
            }

            if (count($update_ids) > 0) {
                $poster_categories_as_salesbox_ones = PosterStore::asSalesboxCategories(
                    PosterStore::findCategory($update_ids)
                );
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
