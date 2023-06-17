<?php

namespace App\Poster\ActionHandlers;


use App\Poster\SalesboxIntegration\SalesboxOffer;
use \App\Salesbox\Facades\SalesboxApi;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;

class ProductActionHandler extends AbstractActionHandler
{

    public function handle(): bool
    {
        $handler = SalesboxApi::getGuzzleHandler();
        // store in per-request cache
        $handler->push(Middleware::mapResponse(function (ResponseInterface $response) {
            return $response;
        }));

        if($this->isAdded() || $this->isRestored() || $this->isChanged()) {
           SalesboxOffer::sync($this->getObjectId());
        }

        if($this->isRemoved()) {
            SalesboxOffer::delete($this->getObjectId());
        }


        return true;
    }


}
