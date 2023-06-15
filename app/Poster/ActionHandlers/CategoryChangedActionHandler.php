<?php

namespace App\Poster\ActionHandlers;

use App\Poster\SalesboxIntegration\SalesboxCategory;
use App\Salesbox\Facades\SalesboxApi;

class CategoryChangedActionHandler extends AbstractActionHandler  {

    public function handle(): bool
    {
        SalesboxApi::authenticate();
        $categories = collect(SalesboxApi::getCategories()['data']);
        $category = $categories->firstWhere('externalId', $this->getObjectId());

        if(!$category) {
            SalesboxCategory::create($this->getObjectId(), $categories);
            return true;
        }

        SalesboxCategory::update($this->getObjectId(), $category, $categories);
        return true;
    }
}
