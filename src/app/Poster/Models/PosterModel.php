<?php

namespace App\Poster\Models;

class PosterModel {
    protected $attributes;
    protected $originalAttributes;

    public function __construct($attributes) {
        $this->originalAttributes = $attributes;
        $this->attributes = $attributes;
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function getOriginalAttributes() {
        return $this->originalAttributes;
    }

}
