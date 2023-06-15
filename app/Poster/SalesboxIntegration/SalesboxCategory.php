<?php

namespace App\Poster\SalesboxIntegration;

use App\Poster\Entities\Category;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxApi;
use Illuminate\Support\Collection;
use poster\src\PosterApi;
use function config;

class SalesboxCategory
{

    static public function create($posterId, Collection $categories) {

        $posterCategoryRes = PosterApi::menu()->getCategory([
            'category_id' => $posterId
        ]);

        Utils::assertResponse($posterCategoryRes, 'getCategory');

        $posterEntity = new Category($posterCategoryRes->response);

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
            $parentCategory = $categories->firstWhere('externalId', $posterId);
            if(!$parentCategory) {
                $parentCategory = self::create($posterId, $categories);
            }
            $newSalesBoxCategory['parentId'] = $parentCategory['internalId'];
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

    static public function update($posterId, $category, Collection $categories) {

        $posterCategoryRes = PosterApi::menu()->getCategory([
            'category_id' => $posterId
        ]);

        Utils::assertResponse($posterCategoryRes, 'getCategory');

        // todo: not sure if need this abstraction
        $posterEntity = new Category($posterCategoryRes->response);

        $names = [
            [
                'name' => $posterEntity->getName(),
                'lang' => 'uk' // todo: should this language be configurable?
            ]
        ];

        $changedCategory = [
            'id' => $category['id'],
            'available' => !$posterEntity->isHidden(),
            'names' => $names,
            // 'externalId' => $posterEntity->getId() // externalId update makes no sense
        ];

        if(!!$posterEntity->getParentCategory()) {
            $parentCategory = $categories->firstWhere('externalId', $posterId);
            if(!$parentCategory) {
                $parentCategory = self::create($posterId, $categories);
            }
            $changedCategory['parentId'] = $parentCategory['internalId'];
        }

        // update photo only if it isn't already present
        if(!isset($salesboxCategory['previewURL']) && $posterEntity->getPhoto()) {
            $url = config('poster.url') . $posterEntity->getPhoto();
            $changedCategory['previewURL'] = $url;
            $changedCategory['originalURL'] = $url;
        }

        $updateManyRes = SalesboxApi::updateCategory([
            'category' => $changedCategory
        ]);

        return $updateManyRes['data']['ids'][0];
    }

}
