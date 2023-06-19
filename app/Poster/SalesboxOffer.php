<?php

namespace App\Poster;

class SalesboxOffer {
    private $available;
    private $names;
    private $descriptions;
    private $photos;
    private $externalId;
    private $categories;
    private $originalUrl;
    private $previewUrl;
    private $units;
    private $stockType;
    private $price;

    /**
     * @return mixed
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * @param mixed $available
     * @return SalesboxOffer
     */
    public function setAvailable($available)
    {
        $this->available = $available;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @param mixed $names
     * @return SalesboxOffer
     */
    public function setNames($names)
    {
        $this->names = $names;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }

    /**
     * @param mixed $descriptions
     * @return SalesboxOffer
     */
    public function setDescriptions($descriptions)
    {
        $this->descriptions = $descriptions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param mixed $photos
     * @return SalesboxOffer
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param mixed $externalId
     * @return SalesboxOffer
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     * @return SalesboxOffer
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    /**
     * @param mixed $originalUrl
     * @return SalesboxOffer
     */
    public function setOriginalUrl($originalUrl)
    {
        $this->originalUrl = $originalUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPreviewUrl()
    {
        return $this->previewUrl;
    }

    /**
     * @param mixed $previewUrl
     * @return SalesboxOffer
     */
    public function setPreviewUrl($previewUrl)
    {
        $this->previewUrl = $previewUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @param mixed $units
     * @return SalesboxOffer
     */
    public function setUnits($units)
    {
        $this->units = $units;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStockType()
    {
        return $this->stockType;
    }

    /**
     * @param mixed $stockType
     * @return SalesboxOffer
     */
    public function setStockType($stockType)
    {
        $this->stockType = $stockType;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     * @return SalesboxOffer
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }
}
