<?php

namespace App\Poster\ActionHandlers;

use App\Salesbox\Facades\SalesboxApi;

class ProductAddedActionHandler extends AbstractActionHandler {

    public function handle(): bool
    {
        SalesboxApi::authenticate();

        return true;
    }
}
