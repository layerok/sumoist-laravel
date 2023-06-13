<?php

namespace App\Poster\ActionHandlers;

interface IAction {
    public function handle(): bool;
}
