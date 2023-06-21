<?php

namespace App\Poster\Models;

use App\Poster\Facades\SalesboxStore;
use App\Poster\meta\PosterProduct_meta;
use App\Poster\Stores\PosterStore;
use App\Poster\Utils;

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
    public function hasModification($modificator_id): bool {
        foreach ($this->getModifications() as $modification) {
            if($modification->getModificatorId() == $modificator_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string|int $modificator_id
     * @return PosterProductModification|null
     */
    public function findModification($modificator_id): ?PosterProductModification {
        foreach ($this->getModifications() as $modification) {
            if($modification->getModificatorId() == $modificator_id) {
                return $modification;
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

    public function asSalesboxOffer(): SalesboxOfferV4
    {
        $salesboxStore = $this->store->getRootStore()->getSalesboxStore();
        $offer = new SalesboxOfferV4([], $salesboxStore);
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

        if ($this->hasPhoto()) {
            $offer->setPreviewURL(Utils::poster_upload_url($this->getPhoto()));
        }

        if ($this->hasPhotoOrigin()) {
            $offer->setOriginalURL(Utils::poster_upload_url($this->getPhotoOrigin()));
        }

        if ($this->getPhoto() && $this->getPhotoOrigin()) {
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

        $category = $salesboxStore->findCategoryByExternalId($this->getMenuCategoryId());

        if ($category) {
            $offer->setCategories([$category->getId()]);
        }

        return $offer;
    }

}
