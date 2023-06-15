<?php

namespace App\Poster\ActionHandlers;

use App\Salesbox\Facades\SalesboxApi;

class CategoryRemovedActionHandler extends AbstractActionHandler
{
    public function handle(): bool
    {
        SalesboxApi::authenticate();
        $salesboxCategory = SalesboxApi::getCategoryByExternalId($this->getObjectId());

        if (!$salesboxCategory) {
            // category doesn't exist in salesbox
            return false;
        }

        SalesboxApi::deleteCategory([
            'id' => $salesboxCategory['id'],
            'recursively' => false
        ], []);

        return true;
    }
}
