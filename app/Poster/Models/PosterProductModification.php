<?php

namespace App\Poster\Models;

use App\Poster\Facades\SalesboxStore;
use App\Poster\meta\PosterProductModification_meta;
use App\Poster\Utils;

/**
 * @class PosterProductModification
 *
 * @property PosterProductModification_meta $attributes
 * @property PosterProductModification_meta $originalAttributes
 */

class PosterProductModification extends PosterModel {
    /**
     * @var PosterProduct $product
     */
    protected $product;

    /**
     * @var PosterProductModificationSpot[] $spots
     */
    protected $spots;

    public function __construct($attributes, PosterProduct $product) {
        parent::__construct($attributes);

        $this->spots = array_map(function($attributes) {
            return new PosterProductModificationSpot($attributes, $this);
        }, $this->attributes->spots);

        $this->product = $product;
    }

    public function getModificatorId() {
        return $this->attributes->modificator_id;
    }

    public function getModificatorName() {
        return $this->attributes->modificator_name;
    }

    public function getModificatorSelfPrice() {
        return $this->attributes->modificator_self_price;
    }

    public function getModificatorSelfPriceNetto() {
        return $this->attributes->modificator_self_price_netto;
    }

    public function getOrder() {
        return $this->attributes->order;
    }

    public function getModificatorBarcode() {
        return $this->attributes->modificator_barcode;
    }

    public function getModificatorProductCode() {
        return $this->attributes->modificator_product_code;
    }

    /**
     * @return PosterProductModificationSpot[]
     */
    public function getSpots() {
        return $this->spots;
    }

    public function getIngredientId() {
        return $this->attributes->ingredient_id;
    }

    public function getFiscalCode() {
        return $this->attributes->fiscal_code;
    }

    public function getMasterId() {
        return $this->attributes->master_id;
    }

    public function getProduct(): PosterProduct {
        return $this->product;
    }

    public function getFirstSpot(): PosterProductModificationSpot {
        return $this->spots[0];
    }

    public function getFirstPrice(): int {
        return intval($this->getFirstSpot()->getPrice() / 100);
    }

    public function asSalesboxOffer() {
        $salesboxStore = $this->product->store->getRootStore()->getSalesboxStore();
        $offer = new SalesboxOffer([], $salesboxStore);
        $offer->setStockType('endless');
        $offer->setUnits('pc');
        $offer->setDescriptions([]);
        $offer->setPhotos([]);
        $offer->setModifierId($this->getModificatorId());
        $offer->setCategories([]);
        $offer->setExternalId($this->product->getProductId());
        $offer->setAvailable($this->getFirstSpot()->isVisible());
        $offer->setPrice($this->getFirstPrice());
        $offer->setNames([
            [
                'name' => $this->product->getProductName() . ' (' . $this->getModificatorName() . ')',
                'lang' => 'uk' // todo: move this value to config, or fetch it from salesbox api
            ]
        ]);

        if ($this->product->hasPhoto()) {
            $offer->setPreviewURL(Utils::poster_upload_url($this->product->getPhoto()));
        }

        if ($this->product->hasPhotoOrigin()) {
            $offer->setOriginalURL(Utils::poster_upload_url($this->product->getPhotoOrigin()));
        }

        if ($this->product->getPhoto() && $this->product->getPhotoOrigin()) {
            $offer->setPhotos([
                [
                    'url' => Utils::poster_upload_url($this->product->getPhotoOrigin()),
                    'previewURL' => Utils::poster_upload_url($this->product->getPhoto()),
                    'order' => 0,
                    'type' => 'image',
                    'resourceType' => 'image'
                ]
            ]);
        }

        $category = SalesboxStore::findCategory($this->product->getMenuCategoryId());

        if ($category) {
            $offer->setCategories([$category->getId()]);
        }

        return $offer;
    }
}
