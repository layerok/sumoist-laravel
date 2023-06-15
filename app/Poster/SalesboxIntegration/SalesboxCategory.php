<?php

namespace App\Poster\SalesboxIntegration;

use App\Poster\Entities\Category;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxApi;
use poster\src\PosterApi;
use function config;

class SalesboxCategory
{
    static public function createIfNotExists($posterId)
    {
        $posterCategoryRes = PosterApi::menu()->getCategory([
            'category_id' => $posterId
        ]);

        Utils::assertResponse($posterCategoryRes, 'getCategory');

        $posterEntity = new Category($posterCategoryRes->response);

        $saleboxCategory = SalesboxApi::getCategoryByExternalId($posterId);

        // if it already exists
        if($saleboxCategory) {
            return $saleboxCategory;
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
            $salesboxParentCategory = self::createIfNotExists($posterEntity->getParentCategory());
            $newSalesBoxCategory['parentId'] = $salesboxParentCategory['internalId'];
        }

        if($posterEntity->getPhoto()) {
            $url = config('poster.url') . $posterEntity->getPhoto();
            $newSalesBoxCategory['previewURL'] = $url;
            $newSalesBoxCategory['originalURL'] = $url;
        }

        $createManyRes = SalesboxApi::createCategory([
            'category' => $newSalesBoxCategory
        ]);

        return $createManyRes['data']['ids'][0];
    }

    static public function updateOrCreateIfNotExists($posterId): ?array {
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
            return self::createIfNotExists($posterEntity->getId());
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
            $salesboxParentCategory = self::createIfNotExists($posterEntity->getParentCategory());;
            $changedSalesBoxCategory['parentId'] = $salesboxParentCategory['internalId'];
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
}
