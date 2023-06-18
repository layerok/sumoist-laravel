<?php

namespace App\Poster\Queries;

use App\Poster\Query;
use App\Salesbox\Facades\SalesboxApi;

class SalesboxAccessTokenQuery extends Query {
    public function __construct()
    {
        parent::__construct(['salesbox', 'accessToken'], function () {
            return SalesboxApi::getAccessToken()->data->token;
        });
    }
}
