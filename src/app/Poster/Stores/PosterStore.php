<?php

namespace App\Poster\Stores;

use App\Poster\Models\PosterCategory;
use App\Poster\Models\PosterProduct;
use App\Poster\Utils;
use Illuminate\Support\Arr;
use poster\src\PosterApi;

/**
 * @see  \App\Poster\Facades\PosterStore
 */
class PosterStore
{
    /** @var PosterCategory[] $categories */
    private $categories = [];

    /**  @var PosterProduct[] $products */
    private $products;

    private $productsLoaded = false;
    private $categoriesLoaded = false;

    public function init() {
        $config = config('poster');
        PosterApi::init([
            'application_id' => $config['application_id'],
            'application_secret' => $config['application_secret'],
            'account_name' => $config['account_name'],
            'access_token' => $config['access_token'],
        ]);
    }

    /**
     * @return PosterProduct[]
     */
    function loadProducts()
    {
        $productsResponse = PosterApi::menu()->getProducts();
        Utils::assertResponse($productsResponse, 'getProducts');

        $this->products = array_map(function ($item) {
            return new PosterProduct($item);
        }, $productsResponse->response);
        $this->productsLoaded = true;

        return $this->products;
    }

    /**
     * @return PosterCategory[]
     */
    function loadCategories()
    {
        $res = PosterApi::menu()->getCategories();
        Utils::assertResponse($res, 'getCategories');

        $this->categories = array_map(function ($item) {
            return new PosterCategory($item);
        }, $res->response);
        $this->categoriesLoaded = true;

        return $this->categories;
    }

    /**
     * @return PosterCategory[]
     */
    function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return PosterProduct[]
     */
    function getProducts()
    {
        return $this->products;
    }

    /**
     * @param $poster_id
     * @return PosterCategory|PosterCategory[]|null
     */
    public function findCategory($poster_id)
    {
        $ids = Arr::wrap($poster_id);
        $found = array_filter($this->categories, function (PosterCategory $category) use ($ids) {
            return in_array($category->getCategoryId(), $ids);
        });
        if (is_array($poster_id)) {
            return $found;
        }
        return array_values($found)[0] ?? null;
    }

    /**
     * @param string|int $poster_id
     * @return bool
     */
    public function categoryExists($poster_id): bool
    {
        return !!$this->findCategory($poster_id);
    }

    /**
     * @param array|string|number $poster_id
     * @return PosterProduct|PosterProduct[]|null
     */
    public function findProduct($poster_id)
    {
        $ids = Arr::wrap($poster_id);
        $found = array_filter($this->products, function (PosterProduct $product) use ($ids) {
            return in_array($product->getProductId(), $ids);
        });
        if (is_array($poster_id)) {
            return $found;
        }
        return array_values($found)[0] ?? null;
    }

    /**
     * @param array|string|number $poster_id
     * @return PosterProduct|PosterProduct[]|null
     */
    public function findDish($poster_id)
    {
        $ids = Arr::wrap($poster_id);
        $found = array_filter($this->products, function (PosterProduct $product) use ($ids) {
            return in_array($product->getProductId(), $ids);
        });
        if (is_array($poster_id)) {
            return $found;
        }
        return array_values($found)[0] ?? null;
    }

    /**
     * @param array $poster_ids
     * @return PosterProduct[]
     */
    public function findProductsWithModifications(array $poster_ids): array
    {
        $found_products = $this->findProduct($poster_ids);

        return array_filter($found_products, function (PosterProduct $posterProduct) {
            return $posterProduct->hasProductModifications();
        });
    }

    /**
     * @param array $poster_ids
     * @return PosterProduct[]
     */
    public function findProductsWithoutModifications(array $poster_ids): array
    {
        $found_products = $this->findProduct($poster_ids);

        return array_filter($found_products, function (PosterProduct $posterProduct) {
            return !$posterProduct->hasProductModifications();
        });
    }

    /**
     * @param array $poster_ids
     * @return PosterProduct[]
     */
    public function findProductsWithoutModificationGroups(array $poster_ids): array
    {
        $found_products = $this->findProduct($poster_ids);

        return array_filter($found_products, function (PosterProduct $posterProduct) {
            return !$posterProduct->hasDishModificationGroups();
        });
    }

    /**
     * @param array $poster_ids
     * @return PosterProduct[]
     */
    public function findProductsWithModificationGroups(array $poster_ids): array
    {
        $found_products = $this->findProduct($poster_ids);

        return array_filter($found_products, function (PosterProduct $posterProduct) {
            return $posterProduct->hasDishModificationGroups();
        });
    }

    /**
     * @param string|int $poster_id
     * @return bool
     */
    public function productExists($poster_id): bool
    {
        return !!$this->findProduct($poster_id);
    }


    /**
     * @return bool
     */
    public function isProductsLoaded(): bool
    {
        return $this->productsLoaded;
    }

    /**
     * @return bool
     */
    public function isCategoriesLoaded(): bool
    {
        return $this->categoriesLoaded;
    }

    /**
     * @return PosterCategory[]
     */
    public function getCategoryParents(PosterCategory $category): array {
        $list = array_map(function($poster_category) {
            return [
                'id' => $poster_category->getCategoryId(),
                'parent_id' => $poster_category->getParentCategory()
            ];
        }, $this->getCategories());

        $parent_ids = array_filter(Utils::find_parents($list, $category->getCategoryId()), function($id) {
            return $id !== "0";
        });

        return array_map(function($parent_id) {
            return $this->findCategory($parent_id);
        }, $parent_ids);
    }

}
