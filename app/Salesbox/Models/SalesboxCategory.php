<?php

namespace App\Salesbox\Models;

class SalesboxCategory extends SalesboxModel {


    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    /**
     * @return mixed
     */
    public function getAvailable()
    {
        return $this->attributes['available'];
    }

    /**
     * @return mixed
     */
    public function getExternalId()
    {
        return $this->attributes['externalId'];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->attributes['id'] ?? null;
    }

    /**
     * @return mixed
     */
    public function getInternalId()
    {
        return $this->attributes['internalId'];
    }

    /**
     * @return mixed
     */
    public function getNames()
    {
        return $this->attributes['names'];
    }

    /**
     * @return mixed
     */
    public function getOriginalURL()
    {
        return $this->attributes['originalURL'] ?? null;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->attributes['parentId'];
    }

    /**
     * @return mixed
     */
    public function getPhotos()
    {
        return $this->attributes['photos'];
    }

    /**
     * @return mixed
     */
    public function getPreviewURL()
    {
        return $this->attributes['previewURL'];
    }

    /**
     * @param mixed $names
     * @return SalesboxCategory
     */
    public function setNames($names)
    {
        $this->attributes['names'] = $names;
        return $this;
    }

    /**
     * @param mixed $parentId
     * @return SalesboxCategory
     */
    public function setParentId($parentId)
    {
        $this->attributes['parentId'] = $parentId;
        return $this;
    }

    /**
     * @param mixed $externalId
     * @return SalesboxCategory
     */
    public function setExternalId($externalId)
    {
        $this->attributes['externalId'] = $externalId;
        return $this;
    }

    /**
     * @param mixed $id
     * @return SalesboxCategory
     */
    public function setId($id)
    {
        $this->attributes['id'] = $id;
        return $this;
    }

    /**
     * @param mixed $internalId
     * @return SalesboxCategory
     */
    public function setInternalId($internalId)
    {
        $this->attributes['internalId'] = $internalId;
        return $this;
    }

    /**
     * @param mixed $photos
     * @return SalesboxCategory
     */
    public function setPhotos($photos)
    {
        $this->attributes['photos'] = $photos;
        return $this;
    }

    /**
     * @param mixed $originalUrl
     * @return SalesboxCategory
     */
    public function setOriginalUrl($originalUrl)
    {
        $this->attributes['originalURL'] = $originalUrl;
        return $this;
    }

    /**
     * @param mixed $previewUrl
     * @return SalesboxCategory
     */
    public function setPreviewUrl($previewUrl)
    {
        $this->attributes['previewURL'] = $previewUrl;
        return $this;
    }

    /**
     * @param mixed $available
     * @return SalesboxCategory
     */
    public function setAvailable($available)
    {
        $this->attributes['available'] = $available;
        return $this;
    }

    public function hasPreviewURL(): bool {
        return !!$this->getPreviewURL();
    }

    public function hasOriginalUrl(): bool {
        return !!$this->getOriginalURL();
    }

}
