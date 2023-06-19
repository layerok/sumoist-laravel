<?php

namespace App\Poster;

class SalesboxCategory {
    private $internalId;
    private $id;
    private $externalId;
    private $parentId;
    private $names;
    private $descriptions;
    private $photos;
    private $originalUrl;
    private $previewUrl;
    private $available;

    /**
     * @return mixed
     */
    public function getAvailable()
    {
        return $this->available;
    }
    /**
     * @return mixed
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }

    /**
     * @return mixed
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getInternalId()
    {
        return $this->internalId;
    }

    /**
     * @return mixed
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @return mixed
     */
    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @return mixed
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @return mixed
     */
    public function getPreviewUrl()
    {
        return $this->previewUrl;
    }

    /**
     * @param mixed $descriptions
     */
    public function setDescriptions($descriptions): void
    {
        $this->descriptions = $descriptions;
    }

    /**
     * @param mixed $names
     * @return SalesboxCategory
     */
    public function setNames($names)
    {
        $this->names = $names;
        return $this;
    }

    /**
     * @param mixed $parentId
     * @return SalesboxCategory
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    /**
     * @param mixed $externalId
     * @return SalesboxCategory
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @param mixed $id
     * @return SalesboxCategory
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param mixed $internalId
     * @return SalesboxCategory
     */
    public function setInternalId($internalId)
    {
        $this->internalId = $internalId;
        return $this;
    }

    /**
     * @param mixed $photos
     * @return SalesboxCategory
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;
        return $this;
    }

    /**
     * @param mixed $originalUrl
     * @return SalesboxCategory
     */
    public function setOriginalUrl($originalUrl)
    {
        $this->originalUrl = $originalUrl;
        return $this;
    }

    /**
     * @param mixed $previewUrl
     * @return SalesboxCategory
     */
    public function setPreviewUrl($previewUrl)
    {
        $this->previewUrl = $previewUrl;
        return $this;
    }

    /**
     * @param mixed $available
     * @return SalesboxCategory
     */
    public function setAvailable($available)
    {
        $this->available = $available;
        return $this;
    }
}
