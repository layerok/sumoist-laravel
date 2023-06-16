<?php

namespace App\Poster\ActionHandlers;

use App\Salesbox\Facades\SalesboxApi;

class CategoryRemovedActionHandler extends AbstractActionHandler
{
    public function handle(): bool
    {
        SalesboxApi::authenticate();
        // recursively=true is important,
        // without this param salesbox will throw an error if the category being deleted has child categories
        SalesboxApi::deleteCategoryByExternalId($this->getObjectId(), true);
        return true;
    }
}
