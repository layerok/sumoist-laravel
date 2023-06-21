<?php

namespace App\Poster\Models;

class PosterProductModification {
    public $attributes;
    public $posterProduct;
    public function __construct($attributes, PosterProduct $posterProduct) {
        $this->attributes = $attributes;
        $this->posterProduct = $posterProduct;
    }
}
