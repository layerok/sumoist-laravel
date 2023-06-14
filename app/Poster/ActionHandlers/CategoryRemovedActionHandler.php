<?php

namespace App\Poster\ActionHandlers;

use App\Salesbox\Facades\SalesboxApi;

class CategoryRemovedActionHandler extends AbstractActionHandler
{
    public function authenticate() {
        $authRes = SalesboxApi::getToken();
        $authData = json_decode($authRes->getBody(), true);
        $token = $authData['data']['token'];

        SalesboxApi:: setHeaders(['Authorization' => sprintf('Bearer %s', $token)]);
    }


    public function handle(): bool
    {
        $this->authenticate();

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
