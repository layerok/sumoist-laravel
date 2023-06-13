<?php

namespace App\Poster\Actions;

use App\Poster\Entities\Category;
use poster\src\PosterApi;
use App\Salesbox\Facades\SalesboxApi;

class CategoryAddedAction extends AbstractAction {

    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function handle(): bool
    {
        $authRes = SalesboxApi::getToken();
        $authData = json_decode($authRes->getBody(), true);

        $access_token =  $authData['data']['token'];
        SalesboxApi::setAccessToken($access_token);

        $salesboxCategory = $this->createSalesboxCategoryByPosterId($this->getObjectId());

        if($salesboxCategory) {
            return true;
        } else {
            // category already exists in salesbox
            return false;
        }
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
            throw new \RuntimeException($posterCategoryRes->message);
        }

        $posterEntity = new Category($posterCategoryRes->response);

        $salesBoxCategoriesRes = SalesboxApi::getCategories();

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
                $salesboxParentCategoryIds = $this->createSalesboxCategoryByPosterId($posterEntity->getParentCategory());
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

        $createManyRes = SalesboxApi::createCategory($newSalesBoxCategory);

        $createManyData = json_decode($createManyRes->getBody(), true);

        return $createManyData['data']['ids'][0];
    }

}
