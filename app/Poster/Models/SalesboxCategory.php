<?php

namespace App\Poster\Models;

use App\Poster\Stores\SalesboxStore;
use App\Poster\Utils;

class SalesboxCategory extends SalesboxModel {
    private $store;

    public function __construct($attributes, SalesboxStore $store) {
        parent::__construct($attributes);
        $this->store = $store;
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

    public function asJson() {
        return [
            'names' => $this->getNames(),
            'available' => $this->getAvailable(),
            'internalId' => $this->getInternalId(),
            'originalURL' => $this->getOriginalURL(),
            'previewURL' => $this->getPreviewURL(),
            'externalId' => $this->getExternalId(),
            'id'=> $this->getId(),
            'parentId' => $this->getParentId(),
            'photos' => $this->getPhotos(),
        ];
    }

    public function updateFromPosterCategory(PosterCategory $posterCategory) {
        $this->setExternalId($posterCategory->getCategoryId());
        $this->setInternalId($posterCategory->getCategoryId());

        $this->setOriginalUrl(null);
        $this->setPreviewUrl(null);

        if($posterCategory->hasPhotoOrigin()) {
            $this->setOriginalUrl(
                Utils::poster_upload_url($posterCategory->getPhotoOrigin())
            );
        }

        if($posterCategory->hasPhoto()) {
            $this->setPreviewUrl(
                Utils::poster_upload_url($posterCategory->getPhoto())
            );
        }

        $this->setParentId(null);

        // check parent category
        if($posterCategory->hasParentCategory()) {
            $this->setParentId($posterCategory->getParentCategory());

            $parent_salesbox_category = $this->store->findCategory($posterCategory->getParentCategory());

            if($parent_salesbox_category) {
                $this->setParentId($parent_salesbox_category->getInternalId());
            }
        }

        $this->setNames([
            [
                'name' => $posterCategory->getCategoryName(),
                'lang' => 'uk'
            ]
        ]);

        $this->setPhotos([]);
        $this->setAvailable($posterCategory->isVisible());

        return clone $this;
    }

    public function delete() {
        return $this->store->deleteCategory($this);
    }
}
