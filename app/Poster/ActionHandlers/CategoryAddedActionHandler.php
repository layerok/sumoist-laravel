<?php

namespace App\Poster\ActionHandlers;

use App\Poster\SalesboxIntegration\SalesboxCategory;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxApi;
use poster\src\PosterApi;

class CategoryAddedActionHandler extends AbstractActionHandler {
    public function handle(): bool
    {
        SalesboxApi::authenticate();
        $salesboxCategories = collect(SalesboxApi::getCategories()['data']);
        $salesboxCategory = $salesboxCategories->firstWhere('externalId', $this->getObjectId());

        if($salesboxCategory) {
            // todo: should I update existing category?
            return false;
        }

        $posterCategoriesRes = PosterApi::menu()->getCategories();
        Utils::assertResponse($posterCategoriesRes, 'getCategories');
        $posterCategories = collect($posterCategoriesRes->response);

        SalesboxCategory::create($this->getObjectId(), $salesboxCategories, $posterCategories);
        return true;
    }

}
