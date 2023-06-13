<?php

namespace App\Poster\Actions;

use App\Poster\Entities\Category;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;
use poster\src\PosterApi;
use App\Salesbox\Facades\SalesboxApi;

class CategoryChangedAction extends AbstractAction  {
    public function handle(): Response
    {
        try {
            $authRes = SalesboxApi::getToken();
        } catch (ClientException $clientException) {
            return response("api error", 200);
        }

        $authData = json_decode($authRes->getBody(), true);

        if (!$authData['success']) {
            return response("Couldn't get salesbox' access token", 200);
        }

        $access_token =  $authData['data']['token'];
        SalesboxApi::setAccessToken($access_token);

        $category = $this->changeSalesboxCategoryByPosterId($this->getObjectId());

        if($category) {
            return response('ok', 200);
        }

        return response(sprintf('Category [%d] created', $this->getObjectId()), 200);
    }

    public function changeSalesboxCategoryByPosterId($posterId): ?array {
        PosterApi::init([
            'account_name' => config('poster.account_name'),
            'access_token' => config('poster.access_token'),
        ]);
        $posterCategoryRes = PosterApi::menu()->getCategory([
            'category_id' => $posterId
        ]);

        if (!isset($posterCategoryRes->response)) {
            return null;
        }

        // todo: not sure if need this abstraction
        $posterEntity = new Category($posterCategoryRes->response);

        try {
            $salesBoxCategoriesRes = SalesboxApi::getCategories();
        } catch (ClientException $clientException) {
            return null;
        }

        $salesboxCategories = json_decode($salesBoxCategoriesRes->getBody(), true);

        $collection = collect($salesboxCategories['data']);

        $saleboxCategory = $collection->firstWhere('externalId', $posterId);

        if(!$saleboxCategory) {
            // if somehow category doesn't already exist in salesbox,
            // then create it first
            $this->createSalesboxCategoryByPosterId($posterEntity->getId());
        }

        $changedSalesBoxCategory = [
            'available' => false,
            'names' => [
                [
                    'name' => $posterEntity->getName(),
                    'lang' => 'uk'
                ]
            ],
            'externalId' => $posterEntity->getId()
        ];

        if(!!$posterEntity->getParentCategory()) {
            $salesboxParentCategory = $collection->firstWhere('externalId', $posterEntity->getParentCategory());

            if($salesboxParentCategory) {
                $changedSalesBoxCategory['parentId'] = $salesboxParentCategory['internalId'];
            } else {
                $createdParentCategory = $this->createSalesboxCategoryByPosterId($posterEntity->getParentCategory());
                if($createdParentCategory) {
                    $changedSalesBoxCategory['parentId'] = $createdParentCategory['internalId'];
                }
            }
        }

        // update photo only if it isn't already present
        if(!isset($salesboxCategory['previewURL']) && $posterEntity->getPhoto()) {
            $url = config('poster.url') . $posterEntity->getPhoto();
            $changedSalesBoxCategory['previewURL'] = $url;
            $changedSalesBoxCategory['originalURL'] = $url;
        }

        try {
            $updateManyRes = SalesboxApi::updateCategory($changedSalesBoxCategory);
        } catch (ClientException $clientException) {
            return null;
        }

        $updateManyData = json_decode($updateManyRes->getBody(), true);

        if(!$updateManyData['success']) {
            return null;
        }

        return $updateManyData['data']['ids'][0];
    }

    public function createSalesboxCategoryByPosterId($posterId): ?array {
        PosterApi::init([
            'account_name' => config('poster.account_name'),
            'access_token' => config('poster.access_token'),
        ]);

        $posterCategoryRes = PosterApi::menu()->getCategory([
            'category_id' => $posterId
        ]);

        if (!isset($posterCategoryRes->response)) {
            return null;
        }

        $posterEntity = new Category($posterCategoryRes->response);

        try {
            $salesBoxCategoriesRes = SalesboxApi::getCategories();
        } catch (ClientException $clientException) {
            return null;
        }

        $salesboxCategories = json_decode($salesBoxCategoriesRes->getBody(), true);

        $collection = collect($salesboxCategories['data']);

        $saleboxCategory = $collection->firstWhere('externalId', $posterId);

        if($saleboxCategory) {
            return null;
        }

        $newSalesBoxCategory = [
            'available' => false,
            'names' => [
                [
                    'name' => $posterEntity->getName(),
                    'lang' => 'uk'
                ]
            ],
            'externalId' => $posterId
        ];

        if(!!$posterEntity->getParentCategory()) {
            $salesboxParentCategory = $collection->firstWhere('externalId', $posterEntity->getParentCategory());

            if($salesboxParentCategory) {
                $newSalesBoxCategory['parentId'] = $salesboxParentCategory['internalId'];
            } else {
                $salesboxParentCategoryIds = $this->createSalesboxCategoryByPosterId($posterEntity->getParentCategory());
                if(is_null($salesboxParentCategoryIds)) {
                    return null;
                }
                $newSalesBoxCategory['parentId'] = $salesboxParentCategoryIds['internalId'];
            }
        }

        if($posterEntity->getPhoto()) {
            $url = config('poster.url') . $posterEntity->getPhoto();
            $newSalesBoxCategory['previewURL'] = $url;
            $newSalesBoxCategory['originalURL'] = $url;
        }

        try {
            $createManyRes = SalesboxApi::createCategory($newSalesBoxCategory);
        } catch (ClientException $clientException) {
            return null;
        }

        $createManyData = json_decode($createManyRes->getBody(), true);

        if($createManyData['success']) {
            return $createManyData['data']['ids'][0]['internalId'];
        }

        return null;
    }
}
