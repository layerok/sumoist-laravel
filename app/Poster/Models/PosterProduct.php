<?php

namespace App\Poster\Models;

use App\Poster\Facades\SalesboxStore;
use App\Poster\meta\PosterProduct_meta;
use App\Poster\Stores\PosterStore;
use App\Poster\Utils;

class PosterProduct {
    /** @property PosterProduct_meta $attributes */
    public $attributes;
    /**
     * @param PosterProduct_meta $attributes
     */

    public $store;
    public function __construct($attributes, PosterStore $store) {
        $this->attributes = $attributes;
        $this->store = $store;
    }

    public function getProductId() {
        return $this->attributes->product_id;
    }

    public function getPhotoOrigin() {
        return $this->attributes->photo_origin;
    }

    public function getPhoto() {
        return $this->attributes->photo;
    }

    public function getMenuCategoryId() {
        return $this->attributes->menu_category_id;
    }

    public function getSpots(): array {
        return $this->attributes->spots;
    }

    public function getFirstSpot() {
        return $this->getSpots()[0];
    }

    public function getCategoryName() {
        return $this->attributes->category_name;
    }

    public function getProductName() {
        return $this->attributes->product_name;
    }

    public function isHidden(): bool {
        // todo: allow choosing a different spot
        $spot = $this->getFirstSpot();
        return $spot->visible == "0";
    }

    public function hasModifications(): bool {
        return isset($this->attributes->modifications);
    }

    public function getPrice(): \stdClass {
        return $this->attributes->price;
    }

    public function getFirstPrice(): int {
        $spot = $this->getFirstSpot();
        return intval($this->getPrice()->{$spot->spot_id}) / 100;
    }

    public function hasPhoto(): bool {
        return !!$this->getPhoto();
    }

    public function hasPhotoOrigin(): bool {
        return !!$this->getPhotoOrigin();
    }


    public function asSalesboxOffer(): SalesboxOffer
    {
        $salesboxStore = $this->store->getRootStore()->getSalesboxStore();
        $offer = new SalesboxOffer([], $salesboxStore);
        $offer->setStockType('endless');
        $offer->setUnits('pc');
        $offer->setDescriptions([]);
        $offer->setPhotos([]);
        $offer->setCategories([]);
        $offer->setExternalId($this->getProductId());
        $offer->setAvailable(!$this->isHidden());
        $offer->setPrice($this->getFirstPrice());
        $offer->setNames([
            [
                'name' => $this->getProductName(),
                'lang' => 'uk' // todo: move this value to config, or fetch it from salesbox api
            ]
        ]);

        if($this->hasPhoto()) {
            $offer->setPreviewURL(Utils::poster_upload_url($this->getPhoto()));
        }

        if($this->hasPhotoOrigin()) {
            $offer->setOriginalURL(Utils::poster_upload_url($this->getPhotoOrigin()));
        }

        if($this->getPhoto() && $this->getPhotoOrigin()) {
            $offer->setPhotos([
                [
                    'url' => Utils::poster_upload_url($this->getPhotoOrigin()),
                    'previewURL' => Utils::poster_upload_url($this->getPhoto()),
                    'order' => 0,
                    'type' => 'image',
                    'resourceType' => 'image'
                ]
            ]);
        }

        $category = SalesboxStore::findCategory($this->getMenuCategoryId());

        if ($category) {
            $offer->setCategories([$category->getId()]);
        }

        return $offer;
    }

}
