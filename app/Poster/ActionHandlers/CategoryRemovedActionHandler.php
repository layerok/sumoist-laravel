<?php

namespace App\Poster\ActionHandlers;

use App\Salesbox\Facades\SalesboxApi;

class CategoryRemovedActionHandler extends AbstractActionHandler
{
    public function handle(): bool
    {
        $authRes = SalesboxApi::getToken();

        $authData = json_decode($authRes->getBody(), true);

        SalesboxApi::setAccessToken($authData['data']['token']);

        $salesboxCategoriesRes = SalesboxApi::getCategories();

        $salesboxCategoriesData = json_decode($salesboxCategoriesRes->getBody(), true);
        $collection = collect($salesboxCategoriesData['data']);
        $salesboxCategory = $collection->firstWhere('externalId', $this->getObjectId());

        if (!$salesboxCategory) {
            // category doesn't exist in salesbox
            return false;
        }

        SalesboxApi::deleteCategory($salesboxCategory['id'], []);

        return true;
    }
}
