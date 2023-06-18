<?php

namespace App\Salesbox\Facades;

use App\Salesbox\meta\SalesboxApiResponse_meta;
use Illuminate\Support\Facades\Facade;

/**
 * Class SalesboxApiV4
 * @method static string authenticate(string $token = '')
 *
 * @method static SalesboxApiResponse_meta getAccessToken(array $params = [])
 * @method static SalesboxApiResponse_meta getOffers(array $params = [], array $guzzleOptions = [])
 *
 * @see  \App\Salesbox\SalesboxApiV4;
 */

class SalesboxApiV4 extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'salesboxapi.v4';
    }
}
