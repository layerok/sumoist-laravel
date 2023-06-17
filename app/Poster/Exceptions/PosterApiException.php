<?php

namespace App\Poster\Exceptions;

class PosterApiException extends \RuntimeException {
    public function __construct($method, $response)
    {
        parent::__construct(sprintf('PosterApi error: %s %s', $method, json_encode($response)));
    }
}
