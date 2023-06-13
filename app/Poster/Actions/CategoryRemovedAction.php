<?php

namespace App\Poster\Actions;

use App\SalesBox\Api;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;

class CategoryRemovedAction extends AbstractAction  {
    public function handle(): Response
    {
        $salesboxApi = new Api();
        try {
            $authRes = $salesboxApi->getToken();
        } catch (ClientException $clientException) {
            return response("api error", 200);
        }

        $authData = json_decode($authRes->getBody(), true);

        if (!$authData['success']) {
            return response("Couldn't get salesbox' access token", 200);
        }

        $access_token =  $authData['data']['token'];
        $salesboxApi->setAccessToken($access_token);

        try {
            $salesboxCategoriesRes = $salesboxApi->getCategories();
        } catch (ClientException $clientException) {
            return response('client error', 200);
        }

        $salesboxCategoriesData = json_decode($salesboxCategoriesRes->getBody(), true);
        $collection = collect($salesboxCategoriesData['data']);
        $salesboxCategory = $collection->firstWhere('externalId', $this->getObjectId());

        if(!$salesboxCategory) {
            return response(sprintf('category [%d] is not found in salesbox', $this->getObjectId()), 200);
        }

        try {
            $salesboxApi->deleteCategory($salesboxCategory['id'], $access_token);
        } catch (ClientException $clientException) {
            return response('client error', 200);
        }

        return response('ok', 200);
    }
}
