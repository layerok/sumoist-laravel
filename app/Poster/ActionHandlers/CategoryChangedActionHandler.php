<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Entities\Category;
use App\Poster\Utils;
use poster\src\PosterApi;
use App\Salesbox\Facades\SalesboxApi;

class CategoryChangedActionHandler extends AbstractActionHandler  {

    public function handle(): bool
    {
        SalesboxApi::authenticate();

        $category = $this->changeSalesboxCategoryByPosterId($this->getObjectId());

        return !!$category;
    }

    public function changeSalesboxCategoryByPosterId($posterId): ?array {
        $posterCategoryRes = PosterApi::menu()->getCategory([
            'category_id' => $posterId
        ]);

        Utils::assertResponse($posterCategoryRes, 'getCategory');

        // todo: not sure if need this abstraction
        $posterEntity = new Category($posterCategoryRes->response);

        $salesboxCategory = SalesboxApi::getCategoryByExternalId($posterId);

        if(!$salesboxCategory) {
            // if somehow category doesn't already exist in salesbox,
            // then create it first
            // no need to update newly created category
            return $this->createSalesboxCategoryByPosterId($posterEntity->getId());
        }

        $names = [
            [
                'name' => $posterEntity->getName(),
                'lang' => 'uk' // todo: should this language be configurable?
            ]
        ];

        $changedSalesBoxCategory = [
            'id' => $salesboxCategory['id'],
            'available' => !$posterEntity->isHidden(),
            'names' => $names,
            // 'externalId' => $posterEntity->getId() // externalId update makes no sense
        ];

        if(!!$posterEntity->getParentCategory()) {
            $salesboxParentCategory = SalesboxApi::getCategoryByExternalId($posterEntity->getParentCategory());

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

        $updateManyRes = SalesboxApi::updateCategory([
            'category' => $changedSalesBoxCategory
        ]);

        return $updateManyRes['data']['ids'][0];
    }

    public function createSalesboxCategoryByPosterId($posterId): ?array {
        $posterCategoryRes = PosterApi::menu()->getCategory([
            'category_id' => $posterId
        ]);

        Utils::assertResponse($posterCategoryRes, 'getCategory');

        $posterEntity = new Category($posterCategoryRes->response);

        $names = [
            [
                'name' => $posterEntity->getName(),
                'lang' => 'uk'// todo: should this language be configurable?
            ]
        ];

        $newSalesboxCategory = [
            'available' => !$posterEntity->isHidden(),
            'names' => $names,
             'externalId' => $posterId
        ];

        if(!!$posterEntity->getParentCategory()) {
            $salesboxParentCategory = SalesboxApi::getCategoryByExternalId($posterEntity->getParentCategory());

            if($salesboxParentCategory) {
                $newSalesboxCategory['parentId'] = $salesboxParentCategory['internalId'];
            } else {
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

        $createManyRes = SalesboxApi::createCategory([
            'category' => $newSalesboxCategory
        ]);

        return $createManyRes['data']['ids'][0];
    }
}
