<?php

namespace App\Salesbox\Facades;

use Illuminate\Support\Facades\Facade;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SalesboxApiV4
 * @method static string authenticate(string $token = '')
 *
 * @method static array getAccessToken(array $params = [])
 * @method static array getOffers(array $params = [], array $guzzleOptions = [])
 * @method static array getOfferByExternalId(string|int $id)
 *
 * @see  \App\Salesbox\SalesboxApiV4;
 */

class SalesboxApiV4 extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'salesboxapi.v4';
    }
}
