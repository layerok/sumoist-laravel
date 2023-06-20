<?php

namespace App\Poster\Models;

use App\Poster\Stores\SalesboxStore;
use App\Poster\Utils;

class SalesboxOffer
{
    public $attributes;
    public $store;

    public function __construct($attributes, SalesboxStore $store)
    {
        $this->attributes = $attributes;
        $this->store = $store;
    }

    /**
     * @return mixed
     */
    public function getAvailable()
    {
        return $this->attributes['available'];
    }

    public function setAvailable($available)
    {
        $this->attributes['available'] = $available;
        return $this;
    }

    public function getId() {
        return $this->attributes['id'] ?? null;
    }

    public function setId($id) {
        $this->attributes['id'] = $id;
        return $this;
    }

    public function getNames()
    {
        return $this->attributes['names'];
    }


    public function setNames($names)
    {
        $this->attributes['names'] = $names;
        return $this;
    }


    public function getDescriptions()
    {
        return $this->attributes['descriptions'];
    }


    public function setDescriptions($descriptions)
    {
        $this->attributes['descriptions'] = $descriptions;
        return $this;
    }

    public function getPhotos()
    {
        return $this->attributes['photos'];
    }

    public function setPhotos($photos)
    {
        $this->attributes['photos'] = $photos;
        return $this;
    }

    public function getExternalId()
    {
        return $this->attributes['externalId'];
    }

    public function setExternalId($externalId)
    {
        $this->attributes['externalId'] = $externalId;
        return $this;
    }

    public function getCategories()
    {
        return $this->attributes['categories'];
    }

    public function setCategories($categories)
    {
        $this->attributes['categories'] = $categories;
        return $this;
    }

    public function getOriginalURL()
    {
        return $this->attributes['originalURL'];
    }

    public function setOriginalURL($originalURL)
    {
        $this->attributes['originalURL'] = $originalURL;
        return $this;
    }

    public function getPreviewURL()
    {
        return $this->attributes['previewURL'];
    }

    public function hasPreviewURL(): bool {
        return !!$this->getPreviewURL();
    }

    public function setPreviewURL($previewURL)
    {
        $this->attributes['previewURL'] = $previewURL;
        return $this;
    }

    public function getUnits()
    {
        return $this->attributes['units'];
    }

    public function setUnits($units)
    {
        $this->attributes['units'] = $units;
        return $this;
    }

    public function getStockType()
    {
        return $this->attributes['stockType'];
    }

    public function setStockType($stockType)
    {
        $this->attributes['stockType'] = $stockType;
        return $this;
    }

    public function getPrice()
    {
        return $this->attributes['price'];
    }

    public function setPrice($price)
    {
        $this->attributes['price'] = $price;
        return $this;
    }

    public function updateFromPosterProduct(PosterProduct $product): SalesboxOffer {
        $this->setExternalId($product->getProductId());
        $this->setAvailable(!$product->isHidden());
        $this->setPrice($product->getFirstPrice());
        $this->setStockType('endless');
        $this->setUnits('pc');
        $this->setCategories([]);
        $this->setPhotos([]);
        $this->setNames([
            [
                'name' => $product->getProductName(),
                'lang' => 'uk' // todo: move this value to config, or fetch it from salesbox api
            ]
        ]);
        // $this->setDescriptions([]);

        if($product->hasPhoto() && !$this->hasPreviewURL()) {
            $this->setPreviewURL(Utils::poster_upload_url($product->getPhoto()));
        }

        if($product->hasPhotoOrigin() && !$this->hasPreviewURL()) {
            $this->setOriginalURL(Utils::poster_upload_url($product->getPhotoOrigin()));
        }

        if(
            $product->hasPhotoOrigin() &&
            $product->hasPhoto() &&
            !$this->hasPreviewURL()
        ) {
            $this->setPhotos([
                [
                    'url' => Utils::poster_upload_url($product->getPhotoOrigin()),
                    'previewURL' => Utils::poster_upload_url($product->getPhoto()),
                    'order' => 0,
                    'type' => 'image',
                    'resourceType' => 'image'
                ]
            ]);
        }

        $category = $this->store->findCategory($product->getMenuCategoryId());

        if ($category) {
            $this->setCategories([$category->getId()]);
        }
        return clone $this;
    }

    public function asJson(): array {
        return [
            'id' => $this->getId(),
            'externalId' => $this->getExternalId(),
            'units' => $this->getUnits(),
            'stockType' => $this->getStockType(),
            'descriptions' => $this->getDescriptions(),
            'photos' => $this->getPhotos(),
            'categories' => $this->getCategories(),
            'names' => $this->getNames(),
            'available' => $this->getAvailable(),
            'price' => $this->getPrice(),
        ];
    }
}
