<?php

namespace App\Poster\Models;

use App\Poster\meta\PosterProduct_meta;

/**
 * @class PosterProduct
 * @property PosterProduct_meta $attributes
 * @property PosterProduct_meta $originalAttributes
 */
class PosterProduct extends PosterModel
{
    /**
     * @var PosterProductSpot[] $spots
     */
    public $spots = [];

    /**
     * @var PosterProductModification[] $modifications
     */
    public $product_modifications = [];

    /**
     * @var PosterDishModificationGroup[] $modifications
     */
    public $dish_modification_groups = [];

    /**
     * @param PosterProduct_meta $attributes
     */
    public function __construct($attributes)
    {
        parent::__construct($attributes);
        if (isset($attributes->spots)) {
            $this->spots = array_map(function ($spotAttributes) {
                return new PosterProductSpot($spotAttributes, $this);
            }, $attributes->spots);
        }
        if (isset($attributes->modifications)) {
            $this->product_modifications = array_map(function ($attributes) {
                return new PosterProductModification($attributes, $this);
            }, $this->attributes->modifications);
        }

        if (isset($attributes->group_modifications)) {
            $this->dish_modification_groups = array_map(function ($attributes) {
                return new PosterDishModificationGroup($attributes, $this);
            }, $this->attributes->group_modifications);
        }

    }

    public function getProductId()
    {
        return $this->attributes->product_id;
    }

    public function getPhotoOrigin()
    {
        return $this->attributes->photo_origin;
    }

    public function getPhoto()
    {
        return $this->attributes->photo;
    }

    public function getMenuCategoryId()
    {
        return $this->attributes->menu_category_id;
    }

    /**
     * @return PosterProductSpot[]
     */
    public function getSpots(): array
    {
        return $this->spots;
    }

    public function getFirstSpot()
    {
        return $this->getSpots()[0];
    }

    public function getCategoryName()
    {
        return $this->attributes->category_name;
    }

    public function getProductName()
    {
        return $this->attributes->product_name;
    }

    public function isHidden(): bool
    {
        // todo: allow choosing a different spot
        $spot = $this->getFirstSpot();
        return $spot->isHidden();
    }

    /**
     * @return PosterDishModificationGroup[]
     */
    public function getDishModificationGroups(): array
    {
        return $this->dish_modification_groups;
    }

    public function hasDishModificationGroups(): bool
    {
        return count($this->dish_modification_groups) > 0;
    }

    public function hasProductModifications(): bool
    {
        return count($this->product_modifications) > 0;
    }

    /**
     * @return PosterProductModification[]
     */
    public function getProductModifications(): array
    {
        return $this->product_modifications;
    }

    /**
     * @param string|int $modificator_id
     * @return bool
     */
    public function hasProductModification($modificator_id): bool
    {
        return !!$this->findProductModification($modificator_id);
    }

    /**
     * @param string|int $modificator_id
     * @return PosterProductModification|null
     */
    public function findProductModification($modificator_id): ?PosterProductModification
    {
        foreach ($this->getProductModifications() as $modification) {
            if ($modification->getModificatorId() == $modificator_id) {
                return $modification;
            }
        }
        return null;
    }

    /**
     * @param string|int $dish_modification_id
     * @return bool
     */
    public function hasDishModification($dish_modification_id): bool
    {
        return !!$this->findDishModification($dish_modification_id);
    }

    /**
     * @param string|int $dish_modification_id
     * @return PosterDishModification|null
     */
    public function findDishModification($dish_modification_id): ?PosterDishModification
    {
        foreach ($this->dish_modification_groups as $group) {
            $dish_modification = $group->findModification($dish_modification_id);
            if ($dish_modification) {
                return $dish_modification;
            }
        }
        return null;
    }

    /**
     * @param string|int $dish_modification_group_id
     * @return bool
     */
    public function hasDishModificationGroup($dish_modification_group_id): bool
    {
        return !!$this->findDishModificationGroup($dish_modification_group_id);
    }

    /**
     * @param string|int $modification_group_id
     * @return PosterDishModificationGroup|null
     */
    public function findDishModificationGroup($modification_group_id): ?PosterProductModification
    {
        foreach ($this->getDishModificationGroups() as $group) {
            if ($group->getGroupId() == $modification_group_id) {
                return $group;
            }
        }
        return null;
    }

    public function getPrice(): ?\stdClass
    {
        return $this->attributes->price;
    }

    public function getFirstPrice(): int
    {
        $spot = $this->getFirstSpot();
        return intval($this->getPrice()->{$spot->getSpotId()}) / 100;
    }

    public function hasPhoto(): bool
    {
        return !!$this->getPhoto();
    }

    public function hasPhotoOrigin(): bool
    {
        return !!$this->getPhotoOrigin();
    }

    public function isDishType(): bool
    {
        return $this->attributes->type === "2";
    }

    /**
     * @param string|int $modification_id
     * @return bool
     */
    public function hasModification($modification_id): bool {
        if($this->isDishType()) {
            return $this->hasDishModification($modification_id);
        }
        return $this->hasProductModification($modification_id);
    }

}
