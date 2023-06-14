<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Entities\Category;
use App\Poster\PosterApiException;
use poster\src\PosterApi;
use App\Salesbox\Facades\SalesboxApi;

class CategoryAddedActionHandler extends AbstractActionHandler {

    public function authenticate() {
        $authRes = SalesboxApi::getToken();
        $authData = json_decode($authRes->getBody(), true);
        $token = $authData['data']['token'];

        SalesboxApi:: setHeaders(['Authorization' => sprintf('Bearer %s', $token)]);
    }

    public function handle(): bool
    {
        $this->authenticate();

        $salesboxCategory = $this->createSalesboxCategoryByPosterId($this->getObjectId());

        return !!$salesboxCategory;
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
            throw new PosterApiException($posterCategoryRes->error);
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

        $names = [
            [
                'name' => $posterEntity->getName(),
                'lang' => 'uk'
            ]
        ];

        $newSalesBoxCategory = [
            'available' => !$posterEntity->isHidden(),
            'names' => $names,
            'externalId' => $posterId
        ];

        if(!!$posterEntity->getParentCategory()) {
            $salesboxParentCategory = $collection->firstWhere('externalId', $posterEntity->getParentCategory());

            if($salesboxParentCategory) {
                $newSalesBoxCategory['parentId'] = $salesboxParentCategory['internalId'];
            } else {
                $newlyCreatedParent = $this->createSalesboxCategoryByPosterId($posterEntity->getParentCategory());
                if(!is_null($newlyCreatedParent)) {
                    $newSalesBoxCategory['parentId'] = $newlyCreatedParent['internalId'];
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
