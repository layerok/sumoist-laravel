<?php

namespace App\Poster\Models;

use App\Poster\meta\PosterProduct_meta;
use App\Poster\Stores\PosterStore;

/**
 * @class PosterProduct
 * @property PosterProduct_meta $attributes
 * @property PosterProduct_meta $originalAttributes
 */
class PosterProduct extends PosterModel
{
    /**
     * @var PosterStore $store
     */
    public $store;

    /**
     * @var PosterProductSpot[] $spots
     */
    public $spots = [];

    /**
     * @var PosterProductModification[] $modifications
     */
    public $modifications = [];

    /**
     * @var PosterDishModificationGroup[] $modifications
     */
    public $modificationGroups = [];

    /**
     * @param PosterProduct_meta $attributes
     * @param PosterStore $store
     */
    public function __construct($attributes, PosterStore $store)
    {
        parent::__construct($attributes);
        if (isset($attributes->spots)) {
            $this->spots = array_map(function ($spotAttributes) {
                return new PosterProductSpot($spotAttributes, $this);
            }, $attributes->spots);
        }
        if (isset($attributes->modifications)) {
            $this->modifications = array_map(function ($attributes) {
                return new PosterProductModification($attributes, $this);
            }, $this->attributes->modifications);
        }

        if (isset($attributes->group_modifications)) {
            $this->modificationGroups = array_map(function ($attributes) {
                return new PosterDishModificationGroup($attributes, $this);
            }, $this->attributes->group_modifications);
        }

        $this->store = $store;
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
    public function getModificationGroups(): array
    {
        return $this->modificationGroups;
    }

    public function hasModificationGroups(): bool
    {
        return count($this->modificationGroups) > 0;
    }

    public function hasModifications(): bool
    {
        return count($this->modifications) > 0;
    }

    /**
     * @return PosterProductModification[]
     */
    public function getModifications(): array
    {
        return $this->modifications;
    }

    /**
     * @param string|int $modificator_id
     * @return bool
     */
    public function hasModification($modificator_id): bool
    {
        return !!$this->findModification($modificator_id);
    }

    /**
     * @param string|int $modificator_id
     * @return PosterProductModification|null
     */
    public function findModification($modificator_id): ?PosterProductModification
    {
        foreach ($this->getModifications() as $modification) {
            if ($modification->getModificatorId() == $modificator_id) {
                return $modification;
            }
        }
        return null;
    }

    /**
     * @param string|int $modification_group_id
     * @return bool
     */
    public function hasModificationGroup($modification_group_id): bool
    {
        return !!$this->findModificationGroup($modification_group_id);
    }

    /**
     * @param string|int $modification_group_id
     * @return PosterDishModificationGroup|null
     */
    public function findModificationGroup($modification_group_id): ?PosterProductModification
    {
        foreach ($this->getModificationGroups() as $group) {
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

    public function asSalesboxOffer(): SalesboxOfferV4
    {
        $salesboxStore = $this->store->getRootStore()->getSalesboxStore();
        $offer = new SalesboxOfferV4([], $salesboxStore);
        $offer->updateFromPosterProduct($this);

        return $offer;
    }

    public function getStore(): PosterStore
    {
        return $this->store;
    }

}
