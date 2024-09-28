<?php

namespace App\Poster\Events\Category;

use App\Poster\Events\PosterWebhookEvent;

class PosterCategoryRestored extends PosterWebhookEvent {

    public function getCategoryId() {
        return $this->object_id;
    }
}
