<?php

namespace App\Poster\Stores;

use App\Poster\SalesboxCategory;
use App\Poster\SalesboxOffer;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;

/**
 * @see  \App\Poster\Facades\SalesboxStore
 */

class SalesboxStore {
    /** @var SalesboxCategory[] $categories */
    public $categories = [];
    public $offers = [];
    public $accessToken;
    public $rootStore;

    public function __construct(RootStore $rootStore) {
        $this->rootStore = $rootStore;
    }

    public function getRootStore(): RootStore {
        return $this->rootStore;
    }

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

    public function findOffer($externalId): ?SalesboxOffer
    {
        foreach($this->offers as $offer) {
            if($offer->getExternalId() === $externalId) {
                return $offer;
            }
        }
        return null;
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

    public function findCategory($externalId): ?SalesboxCategory {
        foreach($this->categories as $category) {
            if($category->getExternalId() === $externalId) {
                return $category;
            }
        }
        return null;
    }

    public function deleteCategory($id) {
        // recursively=true is important,
        // without this param salesbox will throw an error if the category being deleted has child categories
        return SalesboxApi::deleteCategory([
            'id' => $id,
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
}
