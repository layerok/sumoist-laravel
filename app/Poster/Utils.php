<?php

namespace App\Poster;

use App\Poster\Exceptions\PosterApiException;
use App\Poster\meta\PosterApiResponse_meta;
use App\Poster\meta\PosterProduct_meta;

class Utils
{
    /**
     * @param PosterApiResponse_meta $response
     * @param string $method
     * @return PosterApiResponse_meta
     */
    static public function assertResponse($response, $method)
    {
        if (!isset($response->response) || !$response->response) {
            throw new PosterApiException($method, $response);
        }
        return $response;
    }

    static public function productIsHidden(PosterProduct_meta $product, $spot_id): bool
    {
        foreach ($product->spots as $spot) {
            if ($spot_id == $spot->spot_id) {
                return $spot->visible == "0";
            }
        }
        return true;
    }
}
