<?php

namespace App\Poster\Actions;

interface IAction {
    public function handle(): bool;
}
