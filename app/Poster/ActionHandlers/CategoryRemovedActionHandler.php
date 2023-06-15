<?php

namespace App\Poster\ActionHandlers;

use App\Salesbox\Facades\SalesboxApi;

class CategoryRemovedActionHandler extends AbstractActionHandler
{
    public function handle(): bool
    {
        SalesboxApi::authenticate();
        return !!SalesboxApi::deleteCategoryByExternalId($this->getObjectId());
    }
}
