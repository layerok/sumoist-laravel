<?php

namespace App\Poster\Actions;

use App\Salesbox\Facades\SalesboxApi;

class CategoryRemovedAction extends AbstractAction
{
    public function handle(): bool
    {
        $authRes = SalesboxApi::getToken();

        $authData = json_decode($authRes->getBody(), true);

        if (!$authData['success']) {
            // todo: get exception text from salesbox' response
            throw new \RuntimeException("Couldn't get salesbox' access token");
        }

        $access_token = $authData['data']['token'];
        SalesboxApi::setAccessToken($access_token);

        $salesboxCategoriesRes = SalesboxApi::getCategories();

        $salesboxCategoriesData = json_decode($salesboxCategoriesRes->getBody(), true);
        $collection = collect($salesboxCategoriesData['data']);
        $salesboxCategory = $collection->firstWhere('externalId', $this->getObjectId());

        if (!$salesboxCategory) {
            // category doesn't exist in salesbox
            return false;
        }

        SalesboxApi::deleteCategory($salesboxCategory['id']);

        return true;
    }
}
