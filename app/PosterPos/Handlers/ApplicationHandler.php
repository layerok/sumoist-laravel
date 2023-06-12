<?php

namespace App\PosterPos\Handlers;

class ApplicationHandler extends AbstractHandler {
    public function handle(): void {
        if($this->isTest()) {
            $foo = [];
        }
    }
}
