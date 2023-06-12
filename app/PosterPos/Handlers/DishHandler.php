<?php

namespace App\PosterPos\Handlers;

class DishHandler extends AbstractHandler {
    public function handle(): void {
        if($this->isRemoved()) {

        }

        if($this->isAdded()) {
            // add product
        }

        if($this->isChanged()) {
            // change product
        }
    }
}
