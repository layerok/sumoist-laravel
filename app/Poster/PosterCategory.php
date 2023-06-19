<?php

namespace App\Poster;

use App\Poster\Facades\SalesboxStore;
use App\Poster\meta\PosterCategory_meta;
use App\Poster\Stores\PosterStore;

class PosterCategory {
    /** @property PosterCategory_meta $attributes */
    public $attributes;
    /**
     * @param PosterCategory_meta $attributes
     */
    public $store;

    public function __construct($attributes, PosterStore $store) {
        $this->attributes = $attributes;
        $this->store = $store;
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

    /**
     * @return PosterCategory[]
     */
    public function getParents(): array {
        $list = array_map(function($poster_category) {
            return [
                'id' => $poster_category->getCategoryId(),
                'parent_id' => $poster_category->getParentCategory()
            ];
        }, $this->store->getCategories());

        $parent_ids = array_filter(find_parents($list, $this->getCategoryId()), function($id) {
            return $id !== "0";
        });

        return array_map(function($parent_id) {
            return $this->store->findCategory($parent_id);
        }, $parent_ids);
    }

    public function asSalesboxCategory(): SalesboxCategory {

        $salesboxStore = $this->store->getRootStore()->getSalesboxStore();

        if($salesboxStore->categoryExists($this->getCategoryId())) {
            // update category
            $category = $salesboxStore->findCategory($this->getCategoryId());

            if($this->hasPhotoOrigin() && !$category->getPreviewUrl()) {
                $category->setOriginalUrl(
                    Utils::poster_upload_url($this->getPhotoOrigin())
                );
            }

            if($this->hasPhoto() && !$category->getPreviewUrl()) {
                $category->setPreviewUrl(
                    Utils::poster_upload_url($this->getPhoto())
                );
            }

            // check parent category
            if($this->hasParentCategory()) {
                $category->setParentId($this->getParentCategory());

                $parent_salesbox_category = $salesboxStore->findCategory($this->getParentCategory());

                if($parent_salesbox_category) {
                    $category->setParentId($parent_salesbox_category->getInternalId());
                }
            }

            $category->setDescriptions([]);
            $category->setNames([
                [
                    'name' => $this->getCategoryName(),
                    'lang' => 'uk'
                ]
            ]);


            $category->setPhotos([]);
            $category->setAvailable($this->isVisible());
            return $category;
        }


        // create category
        $category = new SalesboxCategory([], $salesboxStore);
        $category->fromPosterCategory($this);
        return $category;
    }

}
