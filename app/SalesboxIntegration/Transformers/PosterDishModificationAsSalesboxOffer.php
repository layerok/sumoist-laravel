<?php

namespace App\SalesboxIntegration\Transformers;

use App\Poster\Models\PosterDishModification;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxStore;
use App\Salesbox\Models\SalesboxOfferV4;

class PosterDishModificationAsSalesboxOffer {

    public $dishModification;
    public function __construct(PosterDishModification $dishModification) {
        $this->dishModification = $dishModification;
    }

    public function transform(): SalesboxOfferV4
    {
        $offer = new SalesboxOfferV4([]);
        $this->updateFrom($offer);

        return $offer;
    }

    public function updateFrom(SalesboxOfferV4 $offer): SalesboxOfferV4 {
        $group = $this->dishModification->getGroup();
        $product = $group->getProduct();

        $offer->setExternalId($product->getProductId());
        $offer->setModifierId($this->dishModification->getDishModificationId());
        $offer->setAvailable(!$product->isHidden());
        $offer->setPrice($this->dishModification->getPrice());
        $offer->setStockType('endless');
        $offer->setUnits('pc');
        $offer->setCategories([]);
        $offer->setPhotos([]);
        $offer->setDescriptions([]);
        $offer->setNames([
            [
                'name' => $product->getProductName() . ', ' . $group->getName() . ': ' . $this->dishModification->getName(),
                'lang' => config('salesbox.lang')
            ]
        ]);

        // set photo of product by default
        if ($product->hasPhoto()) {
            $offer->setPreviewURL(Utils::poster_upload_url($product->getPhoto()));
        }

        if ($product->hasPhotoOrigin()) {
            $offer->setOriginalURL(Utils::poster_upload_url($product->getPhotoOrigin()));
        }

        // but photo of modification is more important
        if ($this->dishModification->getPhotoLarge()) {
            $offer->setPreviewURL(Utils::poster_upload_url($this->dishModification->getPhotoLarge()));
            $offer->setOriginalURL(Utils::poster_upload_url($this->dishModification->getPhotoLarge()));
        }

        if ($product->getPhoto() && $product->getPhotoOrigin()) {
            $offer->setPhotos([
                [
                    'url' => Utils::poster_upload_url($product->getPhotoOrigin()),
                    'previewURL' => Utils::poster_upload_url($product->getPhoto()),
                    'order' => 0,
                    'type' => 'image',
                    'resourceType' => 'image'
                ]
            ]);
        }

        if ($this->dishModification->getPhotoLarge()) {
            $offer->setPhotos([
                [
                    'url' => Utils::poster_upload_url($this->dishModification->getPhotoLarge()),
                    'previewURL' => Utils::poster_upload_url($this->dishModification->getPhotoLarge()),
                    'order' => 0,
                    'type' => 'image',
                    'resourceType' => 'image'
                ]
            ]);
        }

        $category = SalesboxStore::findCategoryByExternalId($product->getMenuCategoryId());

        if ($category) {
            $offer->setCategories([$category->getId()]);
        }
        return clone $offer;
    }

}
