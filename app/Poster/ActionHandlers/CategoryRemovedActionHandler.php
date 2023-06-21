<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Facades\SalesboxStore;

class CategoryRemovedActionHandler extends AbstractActionHandler
{
    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function handle(): bool
    {
        SalesboxStore::authenticate();
        SalesboxStore::loadCategories();

        $salesbox_category = SalesboxStore::findCategoryByExternalId($this->getObjectId());

        if (!$salesbox_category) {
            return false;
        }

        // it also deletes child categories, if they exist
        $salesbox_category->delete();

        return true;
    }


}
