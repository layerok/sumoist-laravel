<?php

namespace App\Poster\Traits;

use Illuminate\Support\Str;

trait HasAttributes {
    public $attributes;
    protected function getAttribute($key) {
        if(!$key) {
            return null;
        }
        if(Str::startsWith($key, 'get')) {
            $attributeKey = Str::snake(Str::substr($key, 3));
            if(property_exists($this->attributes, $attributeKey)) {
                return $this->attributes->{$attributeKey};
            }
        }

        if(property_exists($this->attributes, $key)) {
            return $this->attributes->{$key};
        }

        return null;
    }
}
