<?php

namespace App\Poster\Queries;

use App\Poster\meta\PosterApiResponse_meta;
use App\Poster\Query;
use App\Poster\Utils;
use poster\src\PosterApi;

class PosterCategoriesQuery extends Query {
    public function __construct()
    {
        parent::__construct(['poster', 'categories'], function () {
            /** @var PosterApiResponse_meta $response */
            $response = PosterApi::menu()->getCategories();
            Utils::assertResponse($response, 'getCategories');
            return $response->response;
        });
    }
}
