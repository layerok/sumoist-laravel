<?php

namespace App\Poster\ActionHandlers;

use App\Poster\SalesboxIntegration\SalesboxCategory;
use App\Salesbox\Facades\SalesboxApi;

class CategoryChangedActionHandler extends AbstractActionHandler  {

    public function handle(): bool
    {
        SalesboxApi::authenticate();
        return !!SalesboxCategory::updateOrCreateIfNotExists($this->getObjectId());
    }
}
