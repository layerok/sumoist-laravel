<?php

namespace App\Poster\Queries;

use App\Poster\Query;
use App\Salesbox\Facades\SalesboxApi;

class SalesboxCategoriesQuery extends Query {
    public function __construct()
    {
        parent::__construct(['salesbox', 'categories'], function () {
            return SalesboxApi::getCategories()->data;
        });
    }
}
