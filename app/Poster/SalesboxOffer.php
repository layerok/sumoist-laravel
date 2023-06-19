<?php

namespace App\Poster;

use App\Poster\Stores\SalesboxStore;

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

    public function getOriginalUrl()
    {
        return $this->attributes['originalUrl'];
    }

    public function setOriginalUrl($originalUrl)
    {
        $this->attributes['originalUrl'] = $originalUrl;
        return $this;
    }

    public function getPreviewUrl()
    {
        return $this->attributes['previewUrl'];
    }

    public function setPreviewUrl($previewUrl)
    {
        $this->attributes['previewUrl'] = $previewUrl;
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
}
