<?php

namespace App\Poster;

use App\Poster\Exceptions\PosterApiException;

class Utils
{
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
