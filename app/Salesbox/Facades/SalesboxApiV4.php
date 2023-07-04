<?php

namespace App\Salesbox\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class SalesboxApiV4
 * @method static void setAccessToken(string|null $token)
 * @method static array getAccessToken(array $params = [])
 * @method static array getOffers(array $params = [], array $guzzleOptions = [])
 *
 * @see  \App\Salesbox\SalesboxApiV4;
 */

class SalesboxApiV4 extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'salesboxapi.v4';
    }
}
