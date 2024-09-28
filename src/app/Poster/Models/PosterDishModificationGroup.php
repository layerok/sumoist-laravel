<?php

namespace App\Poster\Models;

use App\Poster\meta\PosterDishGroupModification_meta;
/**
 * @class PosterProductModification
 *
 * @property PosterDishGroupModification_meta $attributes
 * @property PosterDishGroupModification_meta $originalAttributes
 */

class PosterDishModificationGroup extends PosterModel {
    /**
     * @var PosterProduct $product
     */
    protected $product;

    /**
     * @var PosterDishModification[] $spots
     */
    protected $modifications;

    public function __construct($attributes, PosterProduct $product) {
        parent::__construct($attributes);

        $this->modifications = array_map(function($attributes) {
            return new PosterDishModification($attributes, $this);
        }, $this->attributes->modifications);

        $this->product = $product;
    }

    public function getGroupId() {
        return $this->attributes->dish_modification_group_id;
    }

    public function getName() {
        return $this->attributes->name;
    }

    public function getNumMin() {
        return $this->attributes->num_min;
    }

    public function getNumMax() {
        return $this->attributes->num_max;
    }

    public function getType() {
        return $this->attributes->type;
    }

    public function getIsDeleted() {
        return $this->attributes->is_deleted;
    }

    public function isSingleType() {
        return $this->attributes->type === 1;
    }

    public function isMultipleType() {
        return $this->attributes->type === 2;
    }

    /**
     * @return PosterDishModification[]
     */
    public function getModifications(): array {
        return $this->modifications;
    }

    public function getProduct(): PosterProduct {
        return $this->product;
    }

    /**
     * @param $dish_modification_id
     * @return PosterDishModification|null
     */
    public function findModification($dish_modification_id) {
        foreach($this->modifications as $modification) {
            if($modification->getDishModificationId() == $dish_modification_id) {
                return $modification;
            }
        }
        return null;
    }

    /**
     * @param string|int $dish_modification_id
     * @return bool
     */
    public function hasModification($dish_modification_id): bool {
        return !!$this->findModification($dish_modification_id);
    }


}
