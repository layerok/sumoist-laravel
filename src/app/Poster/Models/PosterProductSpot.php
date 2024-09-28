<?php

namespace App\Poster\Models;

use App\Poster\meta\PosterProductSpot_meta;

/**
 * @class PosterProductSpot
 * @property PosterProductSpot_meta $attributes
 */
class PosterProductSpot extends PosterModel {
    /**
     * @var PosterProduct $posterProduct
     */
    protected $product;

    public function __construct($attributes, PosterProduct $product) {
        parent::__construct($attributes);
        $this->product = $product;
    }

    public function getSpotId() {
        return $this->attributes->spot_id;
    }

    public function getPrice() {
        return $this->attributes->price;
    }

    public function getProfit() {
        return $this->attributes->profit;
    }

    public function getProfitNetto() {
        return $this->attributes->profit_netto;
    }

    public function getVisible() {
        return $this->attributes->visible;
    }

    public function isVisible(): bool {
        return $this->attributes->visible === "1";
    }

    public function isHidden(): bool {
        return $this->attributes->visible === "0";
    }

    public function getProduct(): PosterProduct {
        return $this->product;
    }
}
