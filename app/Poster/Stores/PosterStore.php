<?php

namespace App\Poster\Stores;

use App\Poster\Facades\SalesboxStore;
use App\Poster\Models\PosterCategory;
use App\Poster\Models\PosterProduct;
use App\Poster\Models\SalesboxCategory;
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

    /**
     * @var RootStore
     */
    private $rootStore;

    public function __construct(RootStore $rootStore)
    {
        $this->rootStore = $rootStore;
    }

    /**
     * @return RootStore
     */
    public function getRootStore(): RootStore
    {
        return $this->rootStore;
    }

    /**
     * @return PosterProduct[]
     */
    function loadProducts()
    {
        $productsResponse = PosterApi::menu()->getProducts();
        Utils::assertResponse($productsResponse, 'getProducts');

        $this->products = array_map(function ($item) {
            return new PosterProduct($item, $this);
        }, $productsResponse->response);

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
            return new PosterCategory($item, $this);
        }, $res->response);

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
        $found = array_filter($this->categories, function(PosterCategory $category) use($ids) {
            return in_array($category->getCategoryId(), $ids);
        });
        if(is_array($poster_id)) {
            return $found;
        }
        return array_values($found)[0] ?? null;
    }

    /**
     * @param string|int $id
     * @return bool
     */
    public function categoryExists($id): bool {
        return !!$this->findCategory($id);
    }

    /**
     * @param array|string|number $poster_id
     * @return PosterProduct|PosterProduct[]|null
     */
    public function findProduct($poster_id)
    {
        $ids = Arr::wrap($poster_id);
        $found = array_filter($this->products, function(PosterProduct $product) use($ids) {
            return in_array($product->getProductId(), $ids);
        });
        if(is_array($poster_id)) {
            return $found;
        }
        return array_values($found)[0] ?? null;
    }

    /**
     * @param PosterCategory[] $poster_categories
     * @return SalesboxCategory[]
     */
    public function asSalesboxCategories(array $poster_categories): array {
        return array_map(function(PosterCategory $poster_category) {
            if(SalesboxStore::categoryExists($poster_category->getCategoryId())) {
                $salesbox_category = SalesboxStore::findCategory($poster_category->getCategoryId());
                return $salesbox_category->updateFromPosterCategory($poster_category);
            }
            return $poster_category->asSalesboxCategory();
        }, $poster_categories);
    }

}
