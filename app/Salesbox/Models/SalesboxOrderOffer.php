<?php

namespace App\Salesbox\Models;

class SalesboxOrderOffer extends SalesboxModel
{
    public function __construct($attributes)
    {
        parent::__construct($attributes);
    }

    public function getOriginalURL()
    {
        return $this->attributes['originalURL'];
    }

    public function getSpecial()
    {
        return $this->attributes['special'];
    }

    public function isService()
    {
        return $this->attributes['isService'];
    }

    public function getCashback()
    {
        return $this->attributes['cashback'];
    }

    public function getPercentageDiscount()
    {
        return $this->attributes['percentageDiscount'];
    }

    public function getStockType() {
        return $this->attributes['stockType'];
    }

    public function getAllowNegativeStock() {
        return $this->attributes['allowNegativeStock'];
    }

    public function getInternalId() {
        return $this->attributes['internalId'];
    }

    public function getAvailable()
    {
        return $this->attributes['available'];
    }

    public function getUrl() {
        return $this->attributes['url'];
    }

    public function getPreviewURL()
    {
        return $this->attributes['previewURL'];
    }

    public function getPrice()
    {
        return $this->attributes['price'];
    }

    public function getDiscount()
    {
        return $this->attributes['discount'];
    }

    public function getModel()
    {
        return $this->attributes['model'];
    }

    public function getVendor()
    {
        return $this->attributes['vendor'];
    }

    public function getVendorCode()
    {
        return $this->attributes['vendorCode'];
    }

    public function getCountryOfOrigin()
    {
        return $this->attributes['countryOfOrigin'];
    }

    public function getPickup()
    {
        return $this->attributes['pickup'];
    }

    public function getDelivery()
    {
        return $this->attributes['delivery'];
    }

    public function getUnits()
    {
        return $this->attributes['units'];
    }

    public function getMinCount()
    {
        return $this->attributes['minCount'];
    }

    public function getStep()
    {
        return $this->attributes['step'];
    }

    public function getExternalId()
    {
        return $this->attributes['externalId'];
    }

    public function getModifierId() {
        return $this->attributes['modifierId'];
    }

    public function getCategories()
    {
        return $this->attributes['categories'];
    }

    public function getCount()
    {
        return $this->attributes['count'];
    }

    public function getModifiers() {
        return $this->attributes['modifiers'];
    }

    public function getFilterParams()
    {
        return $this->attributes['filterParams'];
    }

    public function getMaxCount()
    {
        return $this->attributes['maxCount'];
    }

    public function getOfferId() {
        return $this->attributes['offerId'];
    }

    public function getName()
    {
        return $this->attributes['name'];
    }

    public function getDescription()
    {
        return $this->attributes['description'];
    }


    public function hasModifierId() {
        return !is_null($this->getModifierId());
    }


    public function hasPreviewURL(): bool {
        return !!$this->getPreviewURL();
    }

}
