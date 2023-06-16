<?php

namespace App\Poster\SalesboxIntegration;

use App\Poster\Entities\Category;
use App\Salesbox\Facades\SalesboxApi;
use Illuminate\Support\Collection;
use function config;

class SalesboxCategory
{
    static public function create($posterId, Collection $salesboxCategories, Collection $posterCategories) {

        $posterCategory = $posterCategories->firstWhere('category_id', $posterId);

        $posterEntity = new Category($posterCategory);

        $newSalesBoxCategory = [
            'available' => !!$posterEntity->getVisible()[0]->visible,
            'names' => [
                [
                    'name' => $posterEntity->getName(),
                    'lang' => 'uk'
                ]
            ],
            'externalId' => $posterId
        ];

        if($posterEntity->getPhoto()) {
            $url = config('poster.url') . $posterEntity->getPhoto();
            $newSalesBoxCategory['previewURL'] = $url;
            $newSalesBoxCategory['originalURL'] = $url;
        }

        if(!!$posterEntity->getParentCategory()) {
            $parentCategory = $salesboxCategories->firstWhere('externalId', $posterEntity->getParentCategory());
            if(!$parentCategory) {
                $parentCategory = self::create($posterEntity->getParentCategory(), $salesboxCategories, $posterCategories);
            }
            $newSalesBoxCategory['parentId'] = $parentCategory['internalId'];
        }

        $createManyRes = SalesboxApi::createCategory([
            'category' => $newSalesBoxCategory
        ]);

        return $createManyRes['data']['ids'][0];
    }

    static public function update($posterId, $salesboxCategory, Collection $salesboxCategories, Collection $posterCategories) {

        $posterCategory = $posterCategories->firstWhere('category_id', $posterId);

        $posterEntity = new Category($posterCategory);

        $changedCategory = [
            'id' => $salesboxCategory['id'],
            'available' => !!$posterEntity->getVisible()[0]->visible,
            'names' => [
                [
                    'name' => $posterEntity->getName(),
                    'lang' => 'uk' // todo: should this language be configurable?
                ]
            ],
        ];

        if(!!$posterEntity->getParentCategory()) {
            $parentCategory = $salesboxCategories->firstWhere('externalId', $posterEntity->getParentCategory());
            if(!$parentCategory) {
                $parentCategory = self::create($posterEntity->getParentCategory(), $salesboxCategories, $posterCategories);
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
