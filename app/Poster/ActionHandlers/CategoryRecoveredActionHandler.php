<?php

namespace App\Poster\ActionHandlers;

use App\Poster\SalesboxIntegration\SalesboxCategory;
use App\Salesbox\Facades\SalesboxApi;

class CategoryRecoveredActionHandler extends AbstractActionHandler {
    public function handle(): bool
    {
        SalesboxApi::authenticate();
        $categories = collect(SalesboxApi::getCategories()['data']);
        $category = $categories->firstWhere('externalId', $this->getObjectId());
        if(!$category) {
            SalesboxCategory::create($this->getObjectId(), $categories);
            return true;
        }
        // todo: should I update existing category?
        return true;
    }

}
