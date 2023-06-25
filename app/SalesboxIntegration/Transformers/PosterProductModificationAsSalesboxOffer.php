<?php

namespace App\SalesboxIntegration\Transformers;


use App\Poster\Models\PosterProductModification;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxStore;
use App\Salesbox\Models\SalesboxOfferV4;

class PosterProductModificationAsSalesboxOffer {

    public $productModification;
    public function __construct(PosterProductModification $productModification) {
        $this->productModification = $productModification;
    }

    public function transform(): SalesboxOfferV4
    {
        $salesboxStore = SalesboxStore::getFacadeRoot();
        $offer = new SalesboxOfferV4([], $salesboxStore);
        $this->updateFrom($offer);

        return $offer;
    }

    public function updateFrom(SalesboxOfferV4 $offer): SalesboxOfferV4 {
        $product = $this->productModification->getProduct();

        $offer->setExternalId($product->getProductId());
        $offer->setModifierId($this->productModification->getModificatorId());
        $offer->setAvailable($this->productModification->isVisible());
        $offer->setPrice($this->productModification->getFirstPrice());
        $offer->setStockType('endless');
        $offer->setUnits('pc');
        $offer->setCategories([]);
        $offer->setPhotos([]);
        $offer->setDescriptions([]);
        $offer->setNames([
            [
                'name' => $product->getProductName() . ' ' . $this->productModification->getModificatorName(),
                'lang' => 'uk' // todo: move this value to config, or fetch it from salesbox api
            ]
        ]);

        if($product->hasPhoto()) {
            $offer->setPreviewURL(Utils::poster_upload_url($product->getPhoto()));
        }

        if($product->hasPhotoOrigin()) {
            $offer->setOriginalURL(Utils::poster_upload_url($product->getPhotoOrigin()));
        }

        if(
            $product->hasPhotoOrigin() &&
            $product->hasPhoto()
        ) {
            $offer->setPhotos([
                [
                    'url' => Utils::poster_upload_url($product->getPhotoOrigin()),
                    'previewURL' => Utils::poster_upload_url($product->getPhoto()),
                    'order' => 0,
                    'type' => 'image',
                    'resourceType' => 'image'
                ]
            ]);
        }

        $category = SalesboxStore::findCategoryByExternalId($product->getMenuCategoryId());

        if ($category) {
            $offer->setCategories([$category->getId()]);
        }
        return clone $offer;
    }

}
