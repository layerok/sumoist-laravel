<?php

namespace App\Poster\ActionHandlers;

use App\Poster\SalesboxIntegration\SalesboxOffer;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;


class ProductAddedActionHandler extends AbstractActionHandler
{

    public function handle(): bool
    {
        $token = SalesboxApi::authenticate();
        SalesboxApiV4::authenticate($token);
        $allOffers = collect(SalesboxApiV4::getOffers()['data']);
        $targetOffers = $allOffers->where('externalId', $this->getObjectId());

        if (!$targetOffers->count() < 1) {
            SalesboxOffer::create($this->getObjectId());
            return true;
        }

        // todo: should I update existing offer?
        return false;
    }
}
