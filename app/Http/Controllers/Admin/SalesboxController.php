<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Poster\Facades\PosterStore;
use App\Poster\Facades\SalesboxStore;
use App\Poster\Models\PosterDishModification;
use App\Poster\Models\PosterDishModificationGroup;
use App\Poster\Models\SalesboxCategory;
use Illuminate\Http\Request;


class SalesboxController extends BaseController
{

    public function index()
    {
        $records = [];
        $this->setPageTitle('Salesbox', 'Синхронизация меню');
        return view('admin.salesbox.index', compact('records'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function syncCategories(Request $request)
    {
        try {
            PosterStore::init();
            SalesboxStore::authenticate();
            $poster_categories = PosterStore::loadCategories();
            $salesbox_categories = SalesboxStore::loadCategories();
            $delete_categories = [];
            $create_categories = [];
            $update_categories = [];

            foreach ($poster_categories as $poster_category) {
                $salesbox_category = SalesboxStore::findCategoryByExternalId($poster_category->getCategoryId());
                if ($salesbox_category) {
                    $update_categories[] = $salesbox_category->updateFromPosterCategory($poster_category);
                } else {
                    $create_categories[] = $poster_category->asSalesboxCategory();
                }
            }

            foreach ($salesbox_categories as $salesbox_category) {
                if ($salesbox_category->getExternalId()) {
                    if (!PosterStore::categoryExists($salesbox_category->getExternalId())) {
                        $delete_categories[] = $salesbox_category;
                    }
                }
                // todo: should I delete categories not connected to poster?
            }
            if (count($create_categories) > 0) {
                SalesboxStore::createManyCategories($create_categories);
            }
            if (count($update_categories) > 0) {

                array_map(function (SalesboxCategory $salesbox_category) {
                    // don't update names and photos
                    $salesbox_category->resetAttributeToOriginalOne('previewURL');
                    $salesbox_category->resetAttributeToOriginalOne('names');
                }, $update_categories);
                SalesboxStore::updateManyCategories($update_categories);
            }
            if (count($delete_categories) > 0) {
                SalesboxStore::deleteManyCategories($delete_categories);
            }
        } catch (\Exception $exception) {
            return $this->responseRedirectBack(sprintf('Возникла ошибка синхронизации категорий. %s', $exception->getMessage()), 'error', true, true);
        }

        return $this->responseRedirectBack('Категории успешно синхронизованы', 'success', false, false);
    }

    public function syncProducts()
    {
        try {
            PosterStore::init();
            SalesboxStore::authenticate();
            $poster_products = PosterStore::loadProducts();
            $salesbox_offers = SalesboxStore::loadOffers();

            $update_offers = [];
            $create_offers = [];
            $delete_offers = [];

            foreach ($salesbox_offers as $offer) {
                if (!$offer->getExternalId()) {
                    // todo: should I delete offers without external id?
                    continue;
                }
                $poster_product = PosterStore::findProduct($offer->getExternalId());
                if (!$poster_product) {
                    $delete_offers[] = $offer;
                    continue;
                }
                if (!$offer->getModifierId()) {
                    continue;
                }
                if (!$poster_product->hasModification($offer->getModifierId())) {
                    $delete_offers[] = $offer;
                }
            }

            foreach ($poster_products as $poster_product) {
                if ($poster_product->hasProductModifications()) {
                    $modifications = $poster_product->getProductModifications();
                    foreach ($modifications as $modification) {
                        $offer = SalesboxStore::findOfferByExternalId($poster_product->getProductId(), $modification->getModificatorId());
                        if ($offer) {
                            $update_offers[] = $offer->updateFromPosterProductModification($modification);
                        } else {
                            $create_offers[] = $modification->asSalesboxOffer();
                        }
                    }
                } else if ($poster_product->hasDishModificationGroups()) {
                    /**
                     * @var PosterDishModification[] $modifications
                     */
                    $modifications = collect($poster_product->getDishModificationGroups())
                        ->filter(function (PosterDishModificationGroup $group) {
                            return $group->isSingleType();
                        })
                        ->map(function (PosterDishModificationGroup $group) {
                            return $group->getModifications();
                        })
                        ->flatten()
                        ->toArray();

                    foreach($modifications as $modification) {
                        $offer = SalesboxStore::findOfferByExternalId($poster_product->getProductId(), $modification->getDishModificationId());
                        if($offer) {
                            $update_offers[] = $offer->updateFromDishModification($modification);
                        } else {
                            $create_offers[] = $modification->asSalesboxOffer();
                        }
                    }
                } else {
                    $offer = SalesboxStore::findOfferByExternalId($poster_product->getProductId());
                    if ($offer) {
                        $update_offers[] = $offer->updateFromPosterProduct($poster_product);
                    } else {
                        $create_offers[] = $poster_product->asSalesboxOffer();
                    }
                }

            }

            if(count($create_offers) > 0) {
                SalesboxStore::createManyOffers($create_offers);
            }

            if(count($update_offers) > 0) {
                SalesboxStore::updateManyOffers($update_offers);
            }

            if(count($delete_offers) > 0) {
                SalesboxStore::deleteManyOffers($delete_offers);
            }


            $foo = [];
        } catch (\Exception $exception) {
            return $this->responseRedirectBack(sprintf('Возникла ошибка синхронизации товаров. %s', $exception->getMessage()), 'error', true, true);
        }
        return $this->responseRedirectBack('Товары успешно синхронизованы', 'success', false, false);
    }

}
