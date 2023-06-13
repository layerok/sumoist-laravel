<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Entities\Category;
use poster\src\PosterApi;
use App\Salesbox\Facades\SalesboxApi;

class CategoryChangedActionHandler extends AbstractActionHandler  {
    public function handle(): bool
    {
        $authRes = SalesboxApi::getToken();

        $authData = json_decode($authRes->getBody(), true);

        $access_token =  $authData['data']['token'];
        SalesboxApi::setAccessToken($access_token);

        $category = $this->changeSalesboxCategoryByPosterId($this->getObjectId());

        if($category) {
            return true;
        }

        // category wasn't updated in salesbox
        return false;
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
            throw new \RuntimeException($posterCategoryRes->message);
        }

        // todo: not sure if need this abstraction
        $posterEntity = new Category($posterCategoryRes->response);

        $salesBoxCategoriesRes = SalesboxApi::getCategories();

        $salesboxCategories = json_decode($salesBoxCategoriesRes->getBody(), true);

        $collection = collect($salesboxCategories['data']);

        $salesboxCategory = $collection->firstWhere('externalId', $posterId);

        if(!$salesboxCategory) {
            // if somehow category doesn't already exist in salesbox,
            // then create it first
            // no need to update newly created category
            return $this->createSalesboxCategoryByPosterId($posterEntity->getId());
        }

        $changedSalesBoxCategory = [
            'id' => $salesboxCategory['id'],
            'available' => !$posterEntity->isHidden(),
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

        $updateManyRes = SalesboxApi::updateCategory($changedSalesBoxCategory);

        $updateManyData = json_decode($updateManyRes->getBody(), true);

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
            throw new \RuntimeException($posterCategoryRes->message);
        }

        $posterEntity = new Category($posterCategoryRes->response);

        $newSalesboxCategory = [
            'available' => !$posterEntity->isHidden(),
            'names' => [
                [
                    'name' => $posterEntity->getName(),
                    'lang' => 'uk'
                ]
            ],
            'externalId' => $posterId
        ];

        if(!!$posterEntity->getParentCategory()) {
            $salesBoxCategoriesRes = SalesboxApi::getCategories();

            $salesboxCategories = json_decode($salesBoxCategoriesRes->getBody(), true);

            $collection = collect($salesboxCategories['data']);

            $salesboxParentCategory = $collection->firstWhere('externalId', $posterEntity->getParentCategory());

            if($salesboxParentCategory) {
                $newSalesboxCategory['parentId'] = $salesboxParentCategory['internalId'];
            }

            if(!$salesboxParentCategory) {
                $salesboxParentCategoryIds = $this->createSalesboxCategoryByPosterId($posterEntity->getParentCategory());
                if(!is_null($salesboxParentCategoryIds)) {
                    $newSalesboxCategory['parentId'] = $salesboxParentCategoryIds['internalId'];
                }
            }
        }

        if($posterEntity->getPhoto()) {
            $url = config('poster.url') . $posterEntity->getPhoto();
            $newSalesboxCategory['previewURL'] = $url;
            $newSalesboxCategory['originalURL'] = $url;
        }

        $createManyRes = SalesboxApi::createCategory($newSalesboxCategory);

        $createManyData = json_decode($createManyRes->getBody(), true);

        return $createManyData['data']['ids'][0];
    }
}
