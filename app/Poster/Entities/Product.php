<?php
namespace App\Poster\Entities;

class Product {
    public $attributes;
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getBarcode(): string {
        return $this->attributes->barcode;
    }

    public function getCategoryName(): string {
        return $this->attributes->category_name;
    }

    public function getUnit(): string {
        return $this->attributes->unit;
    }

    public function getCost(): string {
        return $this->attributes->cost;
    }

    public function getCostNetto(): string {
        return $this->attributes->cost_netto;
    }

    public function getFiscal(): string {
        return $this->attributes->fiscal;
    }

    public function getHidden(): string {
        return $this->attributes->hidden;
    }

    public function isHidden(): bool {
        return $this->getHidden() === "1";
    }

    public function getMenuCategoryId(): string {
        return $this->attributes->menu_category_id;
    }

    public function getWorkshop(): string {
        return $this->attributes->workshop;
    }

    public function getNodiscount(): string {
        return $this->attributes->nodiscount;
    }

    public function getPhoto(): string {
        return $this->attributes->photo;
    }

    public function getPhotoOrigin(): string {
        return $this->attributes->photo_origin;
    }

    public function getPrice($spot_id) {
        return $this->attributes->price->$spot_id;
    }

    public function getProfit($spot_id) {
        return $this->attributes->profit->$spot_id;
    }

    public function getProductCode(): string {
        return $this->attributes->product_code;
    }

    public function getProductId(): string {
        return $this->attributes->product_id;
    }

    public function getProductName(): string {
        return $this->attributes->product_name;
    }

    public function getSortOrder(): string {
        return $this->attributes->sort_order;
    }

    public function getTaxId(): string {
        return $this->attributes->tax_id;
    }

    public function getProductTaxId(): string {
        return $this->attributes->product_tax_id;
    }

    public function getType(): string {
        return $this->attributes->type;
    }

    public function getWeightFlag(): string {
        return $this->attributes->weight_flag;
    }

    public function getColor(): string {
        return $this->attributes->color;
    }

    /**
     * @return object[]
     */
    public function getSpots(): array {
        // stdClass[]
        // spot_id
        // price
        // profit
        // profit_netto
        // visible
        return $this->attributes->spots;
    }

    public function getIngredientId(): string {
        return $this->attributes->ingredient_id;
    }

    public function getDifferentSpotsPrices(): string {
        return $this->attributes->different_spots_prices;
    }

    public function getMasterId(): string {
        return $this->attributes->master_id;
    }

    public function getOut(): int {
        return $this->attributes->out;
    }

}
