<?php

namespace App\Poster\ActionHandlers;

use App\Poster\SalesboxIntegration\SalesboxOffer;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;


class ProductChangedActionHandler extends AbstractActionHandler
{

    public function handle(): bool
    {
        $token = SalesboxApi::authenticate();
        SalesboxApiV4::authenticate($token);

        return !!SalesboxOffer::updateOrCreateIfNotExists($this->getObjectId());
    }
}
