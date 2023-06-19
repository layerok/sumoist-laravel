<?php

namespace App\Poster;

use App\Poster\meta\PosterCategory_meta;

class PosterCategory implements AsSalesboxCategory {
    /** @property PosterCategory_meta $attributes */
    public $attributes;
    /**
     * @param PosterCategory_meta $attributes
     */
    public function __construct($attributes) {
        $this->attributes = $attributes;
    }

    public function getCategoryPhoto() {
        return $this->attributes->category_photo;
    }

    public function getCategoryPhotoOrigin() {
        return $this->attributes->category_photo_origin;
    }

    public function getCategoryId() {
        return $this->attributes->category_id;
    }

    public function getVisible(): array {
        return $this->attributes->visible;
    }

    public function getParentCategory() {
        return $this->attributes->parent_category;
    }

    public function getCategoryName() {
        return $this->attributes->category_name;
    }

    public function hasParentCategory(): bool {
        // I treat "0" as category without parent category
        return !!$this->getParentCategory();
    }

    public function asSalesboxCategory(): SalesboxCategory {
        $category = new SalesboxCategory();
        $category->setId(null);
        $category->setExternalId($this->getCategoryId());
        $category->setInternalId($this->getCategoryId());
        if($this->hasParentCategory()) {
            $category->setParentId($this->getParentCategory());
        } else {
            $category->setParentId(null);
        }

        $category->setDescriptions([]);
        $category->setNames([
            [
                'name' => $this->getCategoryName(),
                'lang' => 'uk'
            ]
        ]);

        $category->setOriginalUrl(null);
        $category->setPreviewUrl(null);


        if($this->getCategoryPhotoOrigin()) {
            $category->setOriginalUrl(
                Utils::poster_upload_url($this->getCategoryPhotoOrigin())
            );
        }

        if($this->getCategoryPhoto()) {
            $category->setPreviewUrl(
                Utils::poster_upload_url($this->getCategoryPhoto())
            );
        }

        $category->setPhotos([]);
        $category->setAvailable(!!$this->getVisible()[0]->visible);

        return $category;
    }

}
