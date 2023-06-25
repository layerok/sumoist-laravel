<?php

namespace App\SalesboxIntegration\Transformers;


use App\Poster\Models\PosterProduct;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxStore;
use App\Salesbox\Models\SalesboxOfferV4;

class PosterProductAsSalesboxOffer {

    public $posterProduct;
    public function __construct(PosterProduct $posterProduct) {
        $this->posterProduct = $posterProduct;
    }

    public function transform(): SalesboxOfferV4
    {
        $salesboxStore = SalesboxStore::getFacadeRoot();
        $offer = new SalesboxOfferV4([], $salesboxStore);
        $this->updateFrom($offer);

        return $offer;
    }

    public function updateFrom(SalesboxOfferV4 $offer): SalesboxOfferV4 {
        $offer->setExternalId($this->posterProduct->getProductId());
        $offer->setAvailable(!$this->posterProduct->isHidden());
        $offer->setPrice($this->posterProduct->getFirstPrice());
        $offer->setStockType('endless');
        $offer->setUnits('pc');
        $offer->setCategories([]);
        $offer->setPhotos([]);
        $offer->setModifierId(null);
        $offer->setDescriptions([]);
        $offer->setNames([
            [
                'name' => $this->posterProduct->getProductName(),
                'lang' => 'uk' // todo: move this value to config, or fetch it from salesbox api
            ]
        ]);

        if($this->posterProduct->hasPhoto()) {
            $offer->setPreviewURL(Utils::poster_upload_url($this->posterProduct->getPhoto()));
        }

        if($this->posterProduct->hasPhotoOrigin()) {
            $offer->setOriginalURL(Utils::poster_upload_url($this->posterProduct->getPhotoOrigin()));
        }

        if(
            $this->posterProduct->hasPhotoOrigin() &&
            $this->posterProduct->hasPhoto()
        ) {
            $offer->setPhotos([
                [
                    'url' => Utils::poster_upload_url($this->posterProduct->getPhotoOrigin()),
                    'previewURL' => Utils::poster_upload_url($this->posterProduct->getPhoto()),
                    'order' => 0,
                    'type' => 'image',
                    'resourceType' => 'image'
                ]
            ]);
        }

        $category = SalesboxStore::findCategoryByExternalId($this->posterProduct->getMenuCategoryId());

        if ($category) {
            $offer->setCategories([$category->getId()]);
        }
        return clone $offer;
    }
}
