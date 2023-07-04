<?php

namespace App\Salesbox\Models;

abstract class SalesboxModel {
    protected $attributes = [];
    protected $originalAttributes = [];

    public function __construct($attributes) {
        $this->attributes = $attributes;
        $this->originalAttributes = $attributes;
    }

    public function getAttributes($key = null) {
        if(!is_null($key)) {
            return $this->attributes[$key];
        }
        return $this->attributes;
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function getOriginalAttributes($key = null) {
        if(!is_null($key)) {
            return $this->originalAttributes[$key] ?? null;
        }
        return $this->originalAttributes;
    }

    public function resetAttributeToOriginalOne($key) {
        $this->attributes[$key] = $this->originalAttributes[$key];
    }
}
