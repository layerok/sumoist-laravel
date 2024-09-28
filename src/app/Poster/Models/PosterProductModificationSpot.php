<?php

namespace App\Poster\Models;

use App\Poster\meta\PosterProductModificationSpot_meta;

/**
 * @class PosterProductModificationSpot
 * @property PosterProductModificationSpot_meta $attributes
 * @property PosterProductModificationSpot_meta $originalAttributes
 */
class PosterProductModificationSpot extends PosterModel {
    /**
     * @var PosterProductModification $posterProductModification
     */
    protected $modification;

    public function __construct($attributes, PosterProductModification $modification) {
        parent::__construct($attributes);
        $this->modification = $modification;
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

    public function getModification(): PosterProductModification {
        return $this->modification;
    }
}
