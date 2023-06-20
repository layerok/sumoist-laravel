<?php

namespace App\Poster\Stores;

use App\Poster\Models\SalesboxCategory;
use App\Poster\Models\SalesboxOffer;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;
use Illuminate\Support\Arr;

/**
 * @see  \App\Poster\Facades\SalesboxStore
 */

class SalesboxStore {

    /** @var SalesboxCategory[] $categories */
    private $categories = [];

    /** @var SalesboxOffer[] $offers */
    private $offers = [];

    /** @var string|null $accessToken */
    private $accessToken;

    /** @var RootStore $rootStore */
    private $rootStore;

    public function __construct(RootStore $rootStore) {
        $this->rootStore = $rootStore;
    }

    /**
     * @return RootStore
     */
    public function getRootStore(): RootStore {
        return $this->rootStore;
    }

    /**
     * @return void
     */
    function authenticate() {
        $this->accessToken = SalesboxApi::getAccessToken()['data']['token'];
        SalesboxApi::authenticate($this->accessToken);
        SalesboxApiV4::authenticate($this->accessToken);
    }

    /**
     * @return SalesboxOffer[]
     */
    public function loadOffers() {
        $this->offers = array_map(function($item) {
            return new SalesboxOffer($item, $this);
        }, SalesboxApiV4::getOffers()['data']);
        return $this->offers;
    }

    /**
     * @return SalesboxOffer[]
     */
    public function getOffers() {
        return $this->offers;
    }

    /**
     * @param $external_id
     * @return SalesboxOffer|SalesboxOffer[]|null
     */
    public function findOffer($external_id)
    {
        $ids = Arr::wrap($external_id);
        $found = array_filter($this->offers, function(SalesboxOffer $offer) use($ids) {
            return in_array($offer->getExternalId(), $ids);
        });
        if(is_array($external_id)) {
            return $found;
        }
        return array_values($found)[0] ?? null;
    }

    public function offerExists($externalId): bool
    {
        return !!$this->findOffer($externalId);
    }

    /**
     * @return SalesboxCategory[]
     */
    public function loadCategories() {
        $this->categories = array_map(function($item) {
            return new SalesboxCategory($item, $this);
        }, SalesboxApi::getCategories()['data']);
        return $this->categories;
    }

    /**
     * @return SalesboxCategory[]
     */
    public function getCategories() {
        return $this->categories;
    }

    public function categoryExists($externalId): bool {
        return !!$this->findCategory($externalId);
    }

    /**
     * @param $external_id
     * @return SalesboxCategory|SalesboxCategory[]|null
     */
    public function findCategory($external_id) {
        $ids = Arr::wrap($external_id);
        $found = array_filter($this->categories, function(SalesboxCategory $category) use($ids) {
            return in_array($category->getExternalId(), $ids);
        });
        if(is_array($external_id)) {
            return $found;
        }
        return array_values($found)[0] ?? null;
    }

    /**
     * @param SalesboxCategory $salesboxCategory
     * @return array
     */
    public function deleteCategory(SalesboxCategory $salesboxCategory) {
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
    public function updateManyCategories($categories) {
        $categories = array_map(function(SalesboxCategory $salesbox_category) {
            return $salesbox_category
                ->asJson();
        }, $categories);

        return SalesboxApi::updateManyCategories([
            'categories' => array_values($categories) // reindex array
        ]);
    }

    /**
     * @param SalesboxCategory[] $categories
     * @return array
     */
    public function createManyCategories($categories) {
        $categories = array_map(function(SalesboxCategory $salesbox_category) {
            return $salesbox_category->asJson();
        }, $categories);

        return SalesboxApi::createManyCategories([
            'categories' => array_values($categories) //reindex array
        ]);
    }

    /**
     * @param SalesboxOffer[] $offers
     * @return array
     */
    public function createManyOffers($offers) {
        $offersAsJson = array_map(function (SalesboxOffer $offer) {
            return $offer->asJson();
        }, $offers);

        return SalesboxApi::createManyOffers([
            'offers' => array_values($offersAsJson)// reindex array, it's important, otherwise salesbox api will fail
        ]);
    }

    /**
     * @param SalesboxOffer[] $offers
     * @return array
     */
    public function updateManyOffers($offers) {
        $offersAsJson = array_map(function (SalesboxOffer $offer) {
            return $offer->asJson();
        }, $offers);

        return SalesboxApi::updateManyOffers([
            'offers' => array_values($offersAsJson)// reindex array, it's important, otherwise salesbox api will fail
        ]);
    }

}
