<?php

namespace App\Poster;

class Utils {
    static public function assertResponse($response, $method) {
        if (!isset($response->response) || !$response->response) {
            throw new PosterApiException($method, $response);
        }
        return $response;
    }
}
