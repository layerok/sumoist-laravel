<?php

namespace App\Poster\Queries;

use App\Poster\Query;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;

class SalesboxOffersQuery extends Query {
    public function __construct()
    {
        parent::__construct(['salesbox', 'offers'], function () {
            return SalesboxApiV4::getOffers()->data;
        });
    }
}
