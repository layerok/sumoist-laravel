<?php

namespace App\Poster;

class PosterApiException extends \RuntimeException {
    public function __construct($error)
    {
        parent::__construct(sprintf('PosterApi error #%d: %s',$error->code, $error->message), $error->code, null);
    }
}
