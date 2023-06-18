<?php

namespace App\Poster\Queries;

use App\Poster\meta\PosterApiResponse_meta;
use App\Poster\Query;
use App\Poster\Utils;
use poster\src\PosterApi;

class PosterProductsQuery extends Query {
    public function __construct()
    {
        parent::__construct(['poster', 'products'], function () {
            /** @var PosterApiResponse_meta $response */
            $response = PosterApi::menu()->getProducts();
            Utils::assertResponse($response, 'getProducts');
            return $response->response;
        });
    }
}
