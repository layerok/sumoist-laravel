<?php

namespace App\Poster\Actions;

use App\Poster\Entities\Category;
use App\SalesBox\Api;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;
use poster\src\PosterApi;

function createSalesboxCategoryByPosterId($posterId, Api $salesboxApi): ?array {
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
        $salesBoxCategoriesRes = $salesboxApi->getCategories();
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
            $salesboxParentCategoryIds = createSalesboxCategoryByPosterId($posterEntity->getParentCategory(), $salesboxApi);
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
        $createManyRes = $salesboxApi->createCategory($newSalesBoxCategory);
    } catch (ClientException $clientException) {
        return null;
    }

    $createManyData = json_decode($createManyRes->getBody(), true);

    if($createManyData['success']) {
        return $createManyData['data']['ids'][0]['internalId'];
    }

    return null;
}

function changeSalesboxCategoryByPosterId($posterId, Api $salesboxApi): ?array {
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
        $salesBoxCategoriesRes = $salesboxApi->getCategories();
    } catch (ClientException $clientException) {
        return null;
    }

    $salesboxCategories = json_decode($salesBoxCategoriesRes->getBody(), true);

    $collection = collect($salesboxCategories['data']);

    $saleboxCategory = $collection->firstWhere('externalId', $posterId);

    if(!$saleboxCategory) {
        // if somehow category doesn't already exist in salesbox,
        // then create it first
        createSalesboxCategoryByPosterId($posterEntity->getId(), $salesboxApi);
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
            $createdParentCategory = createSalesboxCategoryByPosterId($posterEntity->getParentCategory(), $salesboxApi);
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
        $updateManyRes = $salesboxApi->updateCategory($changedSalesBoxCategory);
    } catch (ClientException $clientException) {
        return null;
    }

    $updateManyData = json_decode($updateManyRes->getBody(), true);

    if(!$updateManyData['success']) {
        return null;
    }

    return $updateManyData['data']['ids'][0];
}

class CategoryChangedAction extends AbstractAction  {
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

        $category = changeSalesboxCategoryByPosterId($this->getObjectId(), $salesboxApi);

        if($category) {
            return response('ok', 200);
        }

        return response(sprintf('Category [%d] created', $this->getObjectId()), 200);
    }
}
