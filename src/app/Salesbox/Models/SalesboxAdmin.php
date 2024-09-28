<?php

namespace App\Salesbox\Models;

use App\Salesbox\Stores\SalesboxStore;

class SalesboxAdmin extends SalesboxModel {

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getCompanyId()
    {
        return $this->attributes['companyId'];
    }

    public function getName()
    {
        return $this->attributes['name'];
    }

    public function getRole()
    {
        return $this->attributes['role'];
    }

    public function getPhone()
    {
        return $this->attributes['phone'];
    }

    public function getEmail()
    {
        return $this->attributes['email'];
    }

    public function getPhoto()
    {
        return $this->attributes['photo'];
    }


    public function isAuthorized()
    {
        return $this->attributes['authorized'];
    }
}
