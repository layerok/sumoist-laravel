<?php

namespace App\SalesBox\Entities;

class Category {
    public $attributes;
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }
    public function getId(): string {
        return $this->attributes['id'];
    }
    public function getInternalId(): string {
        return $this->attributes['internalId'];
    }
    public function getExternalId(): ?string {
        return $this->attributes['externalId'];
    }
    public function getPreviewUrl(): string {
        return $this->attributes['previewURL'];
    }
    public function isAvailable(): bool {
        return $this->attributes['available'];
    }
    public function getParentId(): ?string {
        return $this->attributes['parentId'];
    }
    public function getOrder(): ?int {
        return $this->attributes['order'];
    }
    public function getCompanyId(): string {
        return $this->attributes['companyId'];
    }
    public function getNames(): array {
        return $this->attributes['names'];
    }
    public function getName(): string {
        return $this->attributes['name'];
    }

}
