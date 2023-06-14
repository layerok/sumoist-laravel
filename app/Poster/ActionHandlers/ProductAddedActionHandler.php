<?php

namespace App\Poster\ActionHandlers;

use App\Salesbox\Facades\SalesboxApi;

class ProductAddedActionHandler extends AbstractActionHandler {

    public function authenticate() {
        $authRes = SalesboxApi::getToken();
        $authData = json_decode($authRes->getBody(), true);
        $token = $authData['data']['token'];

        SalesboxApi:: setHeaders(['Authorization' => sprintf('Bearer %s', $token)]);
    }

    public function handle(): bool
    {
        $this->authenticate();

        return true;
    }
}
