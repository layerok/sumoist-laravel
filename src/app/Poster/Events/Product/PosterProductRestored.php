<?php

namespace App\Poster\Events\Product;

use App\Poster\Events\PosterWebhookEvent;

class PosterProductRestored extends PosterWebhookEvent {

    public function getProductId() {
        return $this->object_id;
    }
}
