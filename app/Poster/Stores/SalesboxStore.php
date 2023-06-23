<?php

namespace App\Poster\Stores;

use App\Poster\Models\PosterCategory;
use App\Poster\Models\PosterProduct;
use App\Poster\Models\SalesboxCategory;
use App\Poster\Models\SalesboxOfferV4;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;
use Illuminate\Support\Arr;

/**
 * @see  \App\Poster\Facades\SalesboxStore
 */
class SalesboxStore
{

    /** @var SalesboxCategory[] $categories */
    private $categories = [];

    /** @var SalesboxOfferV4[] $offers */
    private $offers = [];

    /** @var string|null $accessToken */
    private $accessToken;

    /** @var RootStore $rootStore */
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
     * @return void
     */
    function authenticate()
    {
        $this->accessToken = SalesboxApi::getAccessToken()['data']['token'];
        SalesboxApi::setAccessToken($this->accessToken);
        SalesboxApiV4::setAccessToken($this->accessToken);
    }

    /**
     * @return SalesboxOfferV4[]
     */
    public function loadOffers()
    {
        $this->offers = array_map(function ($item) {
            return new SalesboxOfferV4($item, $this);
        }, SalesboxApiV4::getOffers([
            'pageSize' => 10000
        ])['data']);
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
            return new SalesboxCategory($item, $this);
        }, SalesboxApi::getCategories()['data']);
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
                    'names' => $category->getNames(),
                    'available' => $category->getAvailable(),
                    'internalId' => $category->getInternalId(),
                    'originalURL' => $category->getOriginalURL(),
                    'previewURL' => $category->getPreviewURL(),
                    'externalId' => $category->getExternalId(),
                    'id'=> $category->getId(),
                    'parentId' => $category->getParentId(),
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
     * @param PosterProduct[] $poster_categories
     * @return SalesboxOfferV4[]|array
     */
    public function updateFromPosterProducts($poster_products)
    {
        $found_poster_products = array_filter($poster_products, function (PosterProduct $poster_product) {
            return $this->offerExistsWithExternalId($poster_product->getProductId());
        });

        return array_map(function (PosterProduct $poster_product) {
            $offer = $this->findOfferByExternalId($poster_product->getProductId());
            return $offer->updateFromPosterProduct($poster_product);
        }, $found_poster_products);
    }

    /**
     * @param PosterCategory[] $poster_categories
     * @return SalesboxCategory[]|array
     */
    public function updateFromPosterCategories($poster_categories)
    {
        $found_poster_categories = array_filter($poster_categories, function (PosterCategory $poster_category) {
            return $this->categoryExistsWithExternalId($poster_category->getCategoryId());
        });
        return array_map(function (PosterCategory $poster_category) {
            $category = $this->findCategoryByExternalId($poster_category->getCategoryId());
            return $category->updateFromPosterCategory($poster_category);
        }, $found_poster_categories);

    }
}
