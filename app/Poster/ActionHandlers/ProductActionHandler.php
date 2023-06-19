<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Facades\PosterStore;
use App\Poster\Facades\SalesboxStore;
use App\Poster\meta\PosterCategory_meta;
use App\Poster\PosterProduct;
use App\Poster\SalesboxOffer;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\meta\CreatedSalesboxCategory_meta;
use App\Salesbox\meta\SalesboxOfferV4_meta;

class ProductActionHandler extends AbstractActionHandler
{
    public $productIdsToUpdate = [];

    public $createdCategories = [];

    /** @var CreatedSalesboxCategory_meta $created_categories */
    public $created_categories;

    public function handle(): bool
    {
        if ($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            SalesboxStore::authenticate();
            SalesboxStore::loadCategories();
            SalesboxStore::loadOffers();
            PosterStore::loadCategories();
            PosterStore::loadProducts();

            $products_ids = $this->findOutWhatProductsNeedToBeCreated();
            $categories_ids = $this->findOutWhatCategoriesNeedToBeCreated();

            if (count($categories_ids) > 0) {
                $categories_to_create = array_filter(PosterStore::getCategories(), function ($poster_category) use ($categories_ids) {
                    return in_array($poster_category->category_id, $categories_ids);
                });
                $this->createCategories($categories_to_create);
            }

            if (count($products_ids) > 0) {

                $simpleOffers = collect(PosterStore::getProducts())
                    ->whereIn('product_id', $products_ids)
                    ->map(function ($attributes) {
                        return new PosterProduct($attributes);
                    })
                    ->filter(function (PosterProduct $poster_product) {
                        return !$poster_product->hasModifications();
                    })
                    ->map(function (PosterProduct $poster_product) {
                        $salesbox_offer = $poster_product->asSalesboxOffer();

                        $salesbox_category = salesbox_fetchCategory($poster_product->getMenuCategoryId());

                        if ($salesbox_category) {
                            $salesbox_offer->setCategories([$salesbox_category->id]);
                        } else {
                            /** @var CreatedSalesboxCategory_meta[]|null $created_categories */
                            $created_categories = perRequestCache()
                                ->get('salesbox.categories.created');

                            /** @var CreatedSalesboxCategory_meta|null $created_category */
                            $created_category = collect($created_categories)
                                ->firstWhere('internalId', $poster_product->getMenuCategoryId());

                            if ($created_category) {
                                $salesbox_offer->setCategories([$created_category->id]);
                            }
                        }

                        return $salesbox_offer;
                    })
                    ->map(function (SalesboxOffer $offer) {
                        return [
                            'externalId' => $offer->getExternalId(),
                            'units' => $offer->getUnits(),
                            'stockType' => $offer->getStockType(),
                            'descriptions' => $offer->getDescriptions(),
                            'photos' => $offer->getPhotos(),
                            'categories' => $offer->getCategories(),
                            'names' => $offer->getNames(),
                            'available' => $offer->getAvailable(),
                            'price' => $offer->getPrice(),
                        ];
                    })
                    ->values()
                    ->toArray();

//                $offers2 = $poster_productsToCreate
//                    ->filter(function($product) {
//                        return !poster_productHasModifications($product);
//                    });
//
//                $foo = [];

                SalesboxApi::createManyOffers([
                    'offers' => $simpleOffers
                ]);

            }

            if (count($this->productIdsToUpdate) > 0) {


                $simpleOffers = collect(PosterStore::getProducts())
                    ->whereIn('product_id', PosterStore::getProducts())
                    ->filter('poster_productWithoutModificators')
                    ->map(function (PosterProduct $poster_product) {
                        $salesbox_offer = $poster_product->asSalesboxOffer();

                        $salesbox_category = salesbox_fetchCategory($poster_product->getMenuCategoryId());

                        if ($salesbox_category) {
                            $salesbox_offer->setCategories([$salesbox_category->id]);
                        } else {
                            /** @var CreatedSalesboxCategory_meta[]|null $created_categories */
                            $created_categories = perRequestCache()
                                ->get('salesbox.categories.created');

                            /** @var CreatedSalesboxCategory_meta|null $created_category */
                            $created_category = collect($created_categories)
                                ->firstWhere('internalId', $poster_product->getMenuCategoryId());

                            if ($created_category) {
                                $salesbox_offer->setCategories([$created_category->id]);
                            }
                        }

                        return $salesbox_offer;
                    })
                    ->map(function (SalesboxOffer $offer) {
                        return [
                            'externalId' => $offer->getExternalId(),
                            'units' => $offer->getUnits(),
                            'stockType' => $offer->getStockType(),
                            'descriptions' => $offer->getDescriptions(),
                            'photos' => $offer->getPhotos(),
                            'categories' => $offer->getCategories(),
                            'names' => $offer->getNames(),
                            'available' => $offer->getAvailable(),
                            'price' => $offer->getPrice(),
                        ];
                    })
                    ->values()
                    ->toArray();

                SalesboxApi::createManyOffers([
                    'offers' => $simpleOffers
                ]);
            }

        }

        if ($this->isRemoved()) {
            SalesboxStore::authenticate();

            $salesbox_offers_ids = collect(salesboxV4_fetchOffers())
                ->where('externalId', $this->getObjectId())
                /** @param SalesboxOfferV4_meta $offer */
                ->map(function ($offer) {
                    return $offer->id;
                })
                ->values()
                ->toArray();


            // delete products
            SalesboxApi::deleteManyOffers([
                'ids' => $salesbox_offers_ids
            ]);

        }


        return true;
    }





    public function findOutWhatProductsNeedToBeCreated()
    {
        $ids = [];
        $salesbox_offer = $this->findOffer($this->getObjectId());
        if (!$salesbox_offer) {
            $ids[] = $this->getObjectId();
        } else {
            $ids[] = $this->getObjectId();
        }
        return $ids;
    }

    /**
     * @param $externalId
     * @return mixed|null
     */
    public function findSalesboxCategory($externalId)
    {
        $key = array_search($externalId, array_column($this->salesbox_categories, 'externalId'));

        if ($key !== false) {
            return $this->salesbox_categories[$key];
        }
        return null;
    }

    /**
     * @param $poster_id
     * @return PosterCategory_meta|null
     */
    public function findPosterCategory($poster_id)
    {
        foreach ($this->poster_categories as $category) {
            if ($category->category_id === $poster_id) {
                return $category;
            }
        }
        return null;
    }

    public function findOutWhatCategoriesNeedToBeCreated(): array
    {
        $ids = [];
        $poster_product = $this->findProduct($this->getObjectId());

        $poster_category = $this->findPosterCategory($poster_product->menu_category_id);
        $salesbox_category = $this->findSalesboxCategory($poster_product->menu_category_id);

        if (!$salesbox_category) {
            $ids[] = $poster_product->menu_category_id;
        }

        if (!!$poster_category->parent_category) {
            $list = array_map(function ($poster_category) {
                return [
                    'id' => $poster_category->category_id,
                    'parent_id' => $poster_category->parent_category
                ];
            }, $this->poster_categories);

            $parent_ids = array_filter(find_parents($list, $poster_category->category_id), function ($id) {
                return $id !== "0";
            });

            foreach ($parent_ids as $parent_id) {
                $salesbox_category = $this->findSalesboxCategory($parent_id);

                // if parent category doesn't exists
                // remember it we will create it later
                if (!$salesbox_category) {
                    $ids[] = $parent_id;
                }
            }
        }
        return $ids;
    }

    public function createCategories($create_categories)
    {
        $categories = [];

        foreach ($create_categories as $poster_category) {
            $categories[] = $this->asJsonForCreate($poster_category);
        }

        return SalesboxApi::createManyCategories([
            'categories' => $categories
        ])['data']['ids'];
    }

    /**
     * @param PosterCategory_meta $poster_category
     * @return array
     */
    public function asJsonForCreate($poster_category)
    {
        $json = [];

        if (!!$poster_category->parent_category) {
            $parent_salesbox_category = $this->findSalesboxCategory($poster_category->parent_category);

            if ($parent_salesbox_category) {
                $json['parentId'] = $parent_salesbox_category['internalId'];
            } else {
                $json['parentId'] = $poster_category->parent_category;
            }
        }

        if ($poster_category->category_photo) {
            $json['previewURL'] = Utils::poster_upload_url($poster_category->category_photo);
        }

        if ($poster_category->category_photo_origin) {
            $json['previewURL'] = Utils::poster_upload_url($poster_category->category_photo_origin);
        }

        $json['available'] = $poster_category->visible[0]->visible === 1;
        $json['externalId'] = $poster_category->category_id;
        $json['names'] = [
            [
                'name' => $poster_category->category_name,
                'lang' => 'uk'
            ]
        ];
        $json['descriptions'] = [];
        $json['photos'] = [];
        $json['internalId'] = $poster_category->category_id;

        return $json;
    }

}
