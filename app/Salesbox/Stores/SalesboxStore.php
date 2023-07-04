<?php

namespace App\Salesbox\Stores;

use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;
use App\Salesbox\Models\SalesboxCategory;
use App\Salesbox\Models\SalesboxOfferV4;
use App\Salesbox\Models\SalesboxOrder;
use Illuminate\Support\Arr;
use function collect;

/**
 * @see  \App\Salesbox\Facades\SalesboxStore
 */
class SalesboxStore
{

    /** @var SalesboxCategory[] $categories */
    private $categories = [];

    /** @var SalesboxOfferV4[] $offers */
    private $offers = [];

    /** @var string|null $accessToken */
    private $accessToken;

    private $categoriesLoaded = false;

    private $offersLoaded = false;


    /**
     * @return void
     */
    function authenticate(string $token = null)
    {
        if($token) {
            $this->accessToken = $token;
        } else {
            $this->accessToken = SalesboxApi::getAccessToken()['data']['token'];
        }

        SalesboxApi::setAccessToken($this->accessToken);
        SalesboxApiV4::setAccessToken($this->accessToken);
    }

    /**
     * @return SalesboxOfferV4[]
     */
    public function loadOffers()
    {
        $this->offers = array_map(function ($item) {
            return new SalesboxOfferV4($item);
        }, SalesboxApiV4::getOffers([
            'pageSize' => 10000
        ])['data']);
        $this->offersLoaded = true;
        return $this->offers;
    }

    /**
     * @return SalesboxOfferV4[]
     */
    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * @param $external_id
     * @return SalesboxOfferV4|SalesboxOfferV4[]|null
     */
    public function findOfferByExternalId($external_id, $modifier_id = null)
    {
        $ids = Arr::wrap($external_id);
        $found = array_filter($this->offers, function (SalesboxOfferV4 $offer) use ($ids, $modifier_id) {
            return in_array($offer->getExternalId(), $ids) && (!$modifier_id || ($offer->getModifierId() == $modifier_id));
        });
        if (is_array($external_id)) {
            return $found;
        }
        return array_values($found)[0] ?? null;
    }

    public function offerExistsWithExternalId($externalId, $modifierId = null): bool
    {
        return !!$this->findOfferByExternalId($externalId, $modifierId);
    }

    /**
     * @return SalesboxCategory[]
     */
    public function loadCategories()
    {
        $this->categories = array_map(function ($item) {
            return new SalesboxCategory($item);
        }, SalesboxApi::getCategories()['data']);
        $this->categoriesLoaded = true;
        return $this->categories;
    }

    /**
     * @return SalesboxCategory[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    public function categoryExistsWithExternalId($externalId): bool
    {
        return !!$this->findCategoryByExternalId($externalId);
    }

    /**
     * @param $external_id
     * @return SalesboxCategory|SalesboxCategory[]|null
     */
    public function findCategoryByExternalId($external_id)
    {
        $ids = Arr::wrap($external_id);
        $found = array_filter($this->categories, function (SalesboxCategory $category) use ($ids) {
            return in_array($category->getExternalId(), $ids);
        });
        if (is_array($external_id)) {
            return $found;
        }
        return array_values($found)[0] ?? null;
    }

    /**
     * @param SalesboxCategory $salesboxCategory
     * @return array
     */
    public function deleteCategory(SalesboxCategory $salesboxCategory)
    {
        // recursively=true is important,
        // without this param salesbox will throw an error if the category being deleted has child categories
        return SalesboxApi::deleteCategory([
            'id' => $salesboxCategory->getId(),
            'recursively' => true
        ], []);
    }

    /**
     * @param SalesboxCategory[] $categories
     * @return array
     */
    public function deleteManyCategories($categories)
    {
        $ids = collect($categories)
            ->map(function (SalesboxCategory $category) {
                return $category->getId();
            })
            ->values()
            ->toArray();

        return SalesboxApi::deleteManyCategories([
            'ids' => $ids,
            'recursively' => true
        ], []);
    }

    /**
     * @param SalesboxCategory[] $categories
     * @return array
     */
    public function updateManyCategories($categories)
    {
        $categories = collect($categories)
            ->map(function (SalesboxCategory $category) {
                return [
                    'id'=> $category->getId(),
                    'internalId' => $category->getInternalId(),
                    'parentId' => $category->getParentId(),
                    'externalId' => $category->getExternalId(),
                    'names' => $category->getNames(),
                    'available' => $category->getAvailable(),
                    'originalURL' => $category->getOriginalURL(),
                    'previewURL' => $category->getPreviewURL(),
                    'photos' => $category->getPhotos(),
                ];
            })
            ->values()
            ->toArray();

        return SalesboxApi::updateManyCategories([
            'categories' => $categories // reindex array
        ]);
    }

    /**
     * @param SalesboxCategory[] $categories
     * @return array
     */
    public function createManyCategories($categories)
    {
        $categories = array_map(function (SalesboxCategory $category) {
            return [
                'names' => $category->getNames(),
                'available' => $category->getAvailable(),
                'internalId' => $category->getInternalId(),
                'originalURL' => $category->getOriginalURL(),
                'previewURL' => $category->getPreviewURL(),
                'externalId' => $category->getExternalId(),
                'parentId' => $category->getParentId(),
                'photos' => $category->getPhotos(),
            ];
        }, $categories);

        return SalesboxApi::createManyCategories([
            'categories' => array_values($categories) //reindex array
        ]);
    }

    /**
     * @param SalesboxOfferV4[] $offers
     * @return array
     */
    public function createManyOffers($offers)
    {
        $offersAsArray = array_map(function (SalesboxOfferV4 $offer) {
            return [
                'externalId' => $offer->getExternalId(),
                'modifierId' => $offer->getModifierId(),
                'units' => $offer->getUnits(),
                'stockType' => $offer->getStockType(),
                'descriptions' => $offer->getDescriptions(),
                'photos' => $offer->getPhotos(),
                'categories' => $offer->getCategories(),
                'names' => $offer->getNames(),
                'available' => $offer->getAvailable(),
                'price' => $offer->getPrice(),
            ];
        }, $offers);

        return SalesboxApi::createManyOffers([
            'offers' => array_values($offersAsArray)// reindex array, it's important, otherwise salesbox api will fail
        ]);
    }

    /**
     * @param SalesboxOfferV4[] $offers
     * @return array
     */
    public function updateManyOffers($offers)
    {
        $offersAsArray = array_map(function (SalesboxOfferV4 $offer) {
            return [
                'id' => $offer->getId(),
                'externalId' => $offer->getExternalId(),
                'modifierId' => $offer->getModifierId(),
                'units' => $offer->getUnits(),
                'stockType' => $offer->getStockType(),
                'descriptions' => $offer->getDescriptions(),
                'categories' => $offer->getCategories(),
                'available' => $offer->getAvailable(),
                'price' => $offer->getPrice(),
                'names' => $offer->getNames(),
                'photos' => $offer->getPhotos(),
            ];
        }, $offers);

        return SalesboxApi::updateManyOffers([
            'offers' => array_values($offersAsArray)// reindex array, it's important, otherwise salesbox api will fail
        ]);
    }

    /**
     * @param SalesboxOfferV4[] $offers
     * @return array
     */
    public function deleteManyOffers($offers)
    {
        $ids = array_map(function (SalesboxOfferV4 $offer) {
            return $offer->getId();
        }, $offers);

        return SalesboxApi::deleteManyOffers([
            'ids' => array_values($ids)
        ]);
    }

    /**
     * @param string $id
     * @return SalesboxOrder|null
     */
    public function getOrderById(string $id): ?SalesboxOrder {
        $res = SalesboxApi::getOrderById($id);
        return new SalesboxOrder($res['data']);
    }

    public function isCategoriesLoaded() :bool {
        return $this->categoriesLoaded;
    }

    public function isOffersLoaded() :bool {
        return $this->offersLoaded;
    }
}
