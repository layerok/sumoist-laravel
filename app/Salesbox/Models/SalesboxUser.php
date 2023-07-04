<?php

namespace App\Salesbox\Models;

class SalesboxUser extends SalesboxModel {

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getUid()
    {
        return $this->attributes['uid'];
    }

    public function getName()
    {
        return $this->attributes['name'];
    }

    public function getOriginalURL()
    {
        return $this->attributes['originalURL'];
    }

    public function getPreviewURL()
    {
        return $this->attributes['previewURL'];
    }

    public function getPhone()
    {
        return $this->attributes['phone'];
    }

    public function getEmail()
    {
        return $this->attributes['email'];
    }

    public function getBalance()
    {
        return $this->attributes['balance'];
    }

    public function getOrderCount()
    {
        return $this->attributes['orderCount'];
    }

    public function getBlockReason()
    {
        return $this->attributes['blockReason'];
    }

    public function getRegistered()
    {
        return $this->attributes['registered'];
    }

    public function getRegisteredAt()
    {
        return $this->attributes['registeredAt'];
    }

    public function getStatus()
    {
        return $this->attributes['status'];
    }

    public function getBirthday()
    {
        return $this->attributes['birthday'];
    }

    public function getCompanyId()
    {
        return $this->attributes['companyId'];
    }

    public function gerRefCode()
    {
        return $this->attributes['refCode'];
    }

    public function getRegistrationCode()
    {
        return $this->attributes['registrationCode'];
    }

    public function getRegistrationBonusesUsed()
    {
        return $this->attributes['registrationBonusesUsed'];
    }

    public function getOrderValue()
    {
        return $this->attributes['orderValue'];
    }

    public function getLastFinishedOrderId()
    {
        return $this->attributes['lastFinishedOrderId'];
    }

    public function getBarcode()
    {
        return $this->attributes['barcode'];
    }

    public function getHaveUnreadNotifications()
    {
        return $this->attributes['haveUnreadNotifications'];
    }

    public function getSex()
    {
        return $this->attributes['sex'];
    }

    public function getCreatedAt()
    {
        return $this->attributes['createdAt'];
    }

    public function getUpdatedAt()
    {
        return $this->attributes['updatedAt'];
    }

    public function getFirstName(): string {
        $names = explode(' ', $this->getName());
        return array_shift($names);
    }

    public function getLastName(): string {
        $names = explode(' ', $this->getName());
        array_shift($names);
        return join(' ', $names);
    }

}
