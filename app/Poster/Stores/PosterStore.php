<?php

namespace App\Poster\Stores;

use App\Poster\PosterCategory;
use App\Poster\PosterProduct;
use App\Poster\Utils;
use poster\src\PosterApi;

class PosterStore {
    /** @var PosterCategory[] $categories */
    public $categories = [];

    /**  @var PosterProduct[] $products */
    public $products;

    /**
     * @var RootStore
     */
    public $rootStore;

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
     * @return PosterProduct[]
     */
    function loadProducts() {
        $productsResponse = PosterApi::menu()->getProducts();
        Utils::assertResponse($productsResponse, 'getProducts');

        $this->products = array_map(function($item) {
            return new PosterProduct($item, $this);
        }, $productsResponse->response);

        return $this->products;
    }

    /**
     * @return PosterCategory[]
     */
    function loadCategories() {
        $res = PosterApi::menu()->getCategories();
        Utils::assertResponse($res, 'getCategories');

        $this->categories = array_map(function($item) {
            return new PosterCategory($item, $this);
        }, $res->response);

        return $this->categories;
    }

    /**
     * @return PosterCategory[]
     */
    function getCategories() {
        return $this->categories;
    }

    /**
     * @return PosterProduct[]
     */
    function getProducts() {
        return $this->products;
    }

    /**
     * @param $poster_id
     * @return PosterCategory|null
     */
    public function findCategory($poster_id) {
        foreach($this->categories as $category) {
            if($category->getCategoryId() === $poster_id) {
                return $category;
            }
        }
        return null;
    }

    /**
     * @param $posterId
     * @return PosterProduct|null
     */
    public function findProduct($posterId)
    {
        foreach ($this->products as $product) {
            if ($product->getProductId() === $posterId) {
                return $product;
            }
        }
        return null;
    }
}
