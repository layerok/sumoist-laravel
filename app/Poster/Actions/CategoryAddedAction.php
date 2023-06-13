<?php

namespace App\Poster\Actions;

use App\Poster\Entities\Category;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;
use poster\src\PosterApi;
use \App\SalesBox\Api;

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

    // if it already exists
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
            if(!is_null($salesboxParentCategoryIds)) {
                $newSalesBoxCategory['parentId'] = $salesboxParentCategoryIds['internalId'];
            }
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
        return $createManyData['data']['ids'][0];
    }

    return null;
}

class CategoryAddedAction extends AbstractAction {

    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function handle(): Response
    {
        // todo: make salesbox api singleton
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

        $salesboxCategory = createSalesboxCategoryByPosterId($this->getObjectId(), $salesboxApi);

        if($salesboxCategory) {
            return response('ok', 200);
        }

        return response(sprintf("Category [%d] wasn't created", $this->getObjectId()), 200);
    }


}
