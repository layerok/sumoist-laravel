<?php

use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxStore;
use App\Salesbox\Models\SalesboxOrder;
use \poster\src\PosterApi;

Route::match(['get', 'post'], '/salesbox-webhook', function () {
    SalesboxStore::authenticate();
    $response = SalesboxApi::getOrderById('d71876ce-c3e7-4b87-be1e-283fea769ea3');
    $order = new SalesboxOrder($response['data']);
    $offers = $order->getOffers();

    PosterApi::init(config('poster'));
    PosterApi::incomingOrders()
        ->createIncomingOrder([

        ]);

    return response('ok');
});

