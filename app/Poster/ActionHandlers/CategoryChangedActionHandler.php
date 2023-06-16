<?php

namespace App\Poster\ActionHandlers;

use App\Poster\SalesboxIntegration\SalesboxCategory;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxApi;
use poster\src\PosterApi;

class CategoryChangedActionHandler extends AbstractActionHandler  {

    public function handle(): bool
    {
        SalesboxApi::authenticate();
        $salesboxCategories = collect(SalesboxApi::getCategories()['data']);
        $salesboxCategory = $salesboxCategories->firstWhere('externalId', $this->getObjectId());

        $posterCategoriesRes = Utils::assertResponse(PosterApi::menu()->getCategories(), 'getCategories');
        Utils::assertResponse($posterCategoriesRes, 'getCategories');
        $posterCategories = collect($posterCategoriesRes->response);

        if(!$salesboxCategory) {
            SalesboxCategory::create($this->getObjectId(), $salesboxCategories, $posterCategories);
            return true;
        }

        SalesboxCategory::update($this->getObjectId(), $salesboxCategory, $salesboxCategories, $posterCategories);
        return true;
    }
}
