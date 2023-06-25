<?php

namespace App\Poster\Models;

use App\Poster\meta\PosterDishModification_meta;
use App\Poster\Utils;

/**
 * @class PosterProductModification
 *
 * @property PosterDishModification_meta $attributes
 * @property PosterDishModification_meta $originalAttributes
 */

class PosterDishModification extends PosterModel {
    /**
     * @var PosterDishModificationGroup $product
     */
    protected $group;

    public function __construct($attributes, PosterDishModificationGroup $group) {
        parent::__construct($attributes);

        $this->group = $group;
    }

    public function getDishModificationId() {
        return $this->attributes->dish_modification_id;
    }

    public function getName() {
        return $this->attributes->name;
    }

    public function getIngredientId() {
        return $this->attributes->ingredient_id;
    }

    public function getType() {
        return $this->attributes->type;
    }

    public function getBrutto() {
        return $this->attributes->brutto;
    }

    public function getPrice(): int {
        return $this->attributes->price;
    }

    public function getPhotoOrig() {
        // I don't know why, but photo_orig is always empty
        return $this->attributes->photo_orig;
    }

    public function getPhotoLarge() {
        return $this->attributes->photo_large;
    }

    public function getPhotoSmall() {
        // photo_small can have very bad quality, sure it depends on original photo quality
        return $this->attributes->photo_small;
    }

    public function getLastModifiedTime() {
        return $this->attributes->last_modified_time;
    }

    public function getGroup() {
        return $this->group;
    }
}
