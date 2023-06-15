<?php

namespace App\Poster\ActionHandlers;

use App\Poster\SalesboxIntegration\SalesboxOffer;
use App\Salesbox\Facades\SalesboxApi;

class ProductRecoveredActionHandler extends AbstractActionHandler {

    public function handle(): bool
    {
        SalesboxApi::authenticate();
        return !!SalesboxOffer::createIfNotExists($this->getObjectId());
    }
}
