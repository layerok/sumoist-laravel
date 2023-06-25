<?php

namespace App\SalesboxIntegration\Transformers;


use App\Poster\Models\PosterCategory;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxStore;
use App\Salesbox\Models\SalesboxCategory;

class PosterCategoryAsSalesboxCategory {

    public $posterCategory;
    public function __construct(PosterCategory $posterCategory) {
        $this->posterCategory = $posterCategory;
    }

    public function transform() {

        // create category
        $category = new SalesboxCategory([]);
        $this->updateFrom($category);

        return $category;
    }


    public function updateFrom(SalesboxCategory $category) {
        $category->setExternalId($this->posterCategory->getCategoryId());
        $category->setInternalId($this->posterCategory->getCategoryId());

        $category->setOriginalUrl(null);
        $category->setPreviewUrl(null);

        if($this->posterCategory->hasPhotoOrigin()) {
            $category->setOriginalUrl(
                Utils::poster_upload_url($this->posterCategory->getPhotoOrigin())
            );
        }

        if($this->posterCategory->hasPhoto()) {
            $category->setPreviewUrl(
                Utils::poster_upload_url($this->posterCategory->getPhoto())
            );
        }

        $category->setParentId(null);

        // check parent category
        if($this->posterCategory->hasParentCategory()) {
            $category->setParentId($this->posterCategory->getParentCategory());

            $parent_salesbox_category = SalesboxStore::findCategoryByExternalId($this->posterCategory->getParentCategory());

            if($parent_salesbox_category) {
                $category->setParentId($parent_salesbox_category->getInternalId());
            }
        }

        $category->setNames([
            [
                'name' => $this->posterCategory->getCategoryName(),
                'lang' => config('salesbox.lang')
            ]
        ]);

        $category->setPhotos([]);
        $category->setAvailable($this->posterCategory->isVisible());

        return clone $category;
    }


}
