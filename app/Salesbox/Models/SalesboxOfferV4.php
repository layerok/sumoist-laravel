<?php

namespace App\Salesbox\Models;

use App\Salesbox\Stores\SalesboxStore;

class SalesboxOfferV4 extends SalesboxModel
{
    public function __construct($attributes)
    {
        parent::__construct($attributes);
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

    public function getModifierId() {
        return $this->attributes['modifierId'] ?? null;
    }

    public function setModifierId($modifierId)
    {
        $this->attributes['modifierId'] = $modifierId;
        return $this;
    }

    public function hasModifierId() {
        return !is_null($this->getModifierId());
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

}
