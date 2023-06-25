<?php

namespace App\SalesboxIntegration\Handlers;

interface IAction {
    public function handle(): bool;
}
