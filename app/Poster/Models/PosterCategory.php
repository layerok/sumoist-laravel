<?php

namespace App\Poster\Models;

use App\Poster\meta\PosterCategory_meta;
use App\Poster\Stores\PosterStore;

class PosterCategory {
    /** @var PosterCategory_meta $attributes */
    public $attributes;

    public function __construct($attributes) {
        $this->attributes = $attributes;
    }

    public function getPhoto() {
        return $this->attributes->category_photo;
    }

    public function getPhotoOrigin() {
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

    public function hasPhoto(): bool {
        return !!$this->getPhoto();
    }

    public function hasPhotoOrigin(): bool {
        return !!$this->getPhotoOrigin();
    }

    public function isVisible(): bool {
        return !!$this->getVisible()[0]->visible;
    }
}
