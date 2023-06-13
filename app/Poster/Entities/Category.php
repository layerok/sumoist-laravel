<?php
namespace App\Poster\Entities;

class Category {
    public $attributes;
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getName() {
        return $this->attributes->category_name;
    }

    public function getPhoto() {
        return $this->attributes->category_photo;
    }

    public function getSortOrder() {
        return $this->attributes->sort_order;
    }

    public function getLeft() {
        return $this->attributes->left;
    }

    public function getRight() {
        return $this->attributes->right;
    }

    public function getLevel() {
        return $this->attributes->level;
    }

    public function isHidden(): bool {
        return $this->attributes->category_hidden;
    }

    public function getParentCategory() {
        return $this->attributes->parent_category;
    }

    public function getId() {
        return $this->attributes->category_id;
    }

    public function getColor() {
        return $this->attributes->category_color;
    }

    public function getTag() {
        return $this->attributes->category_tag;
    }

    public function getTaxId() {
        return $this->attributes->tax_id;
    }

    public function getFiscal() {
        return $this->attributes->fiscal;
    }

    public function getNoDiscount() {
        return $this->attributes->nodiscount;
    }

    public function getVisible() {
        // [{ spot_id, visible }]
        return $this->attributes->visible;
    }


}
