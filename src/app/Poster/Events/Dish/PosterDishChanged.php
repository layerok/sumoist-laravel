<?php

namespace App\Poster\Events\Dish;

use App\Poster\Events\PosterWebhookEvent;

class PosterDishChanged extends PosterWebhookEvent {

    public function getProductId() {
        return $this->object_id;
    }
}
