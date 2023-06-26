<?php

namespace App\Poster\Events\Dish;

use App\Poster\Events\PosterWebhookEvent;

class PosterDishRemoved extends PosterWebhookEvent {

    public function getProductId() {
        return $this->object_id;
    }
}
