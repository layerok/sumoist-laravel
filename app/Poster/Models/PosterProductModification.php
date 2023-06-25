<?php

namespace App\Poster\Models;

use App\Poster\meta\PosterProductModification_meta;

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

    public function isVisible(): bool {
        return $this->getFirstSpot()->isVisible();
    }

    public function isHidden(): bool {
        return $this->getFirstSpot()->isHidden();
    }
}
