<?php

namespace App\Poster;

use App\Poster\Exceptions\PosterApiException;
use App\Poster\meta\PosterApiResponse_meta;

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

    static public function poster_upload_url($path) {
        return config('poster.url') . $path;
    }
}
