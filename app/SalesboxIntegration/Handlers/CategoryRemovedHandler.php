<?php

namespace App\SalesboxIntegration\Handlers;

use App\Salesbox\Facades\SalesboxStore;

class CategoryRemovedHandler extends AbstractHandler
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
        SalesboxStore::deleteCategory($salesbox_category);

        return true;
    }


}
