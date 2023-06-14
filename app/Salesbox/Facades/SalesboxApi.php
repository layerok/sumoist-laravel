<?php

namespace App\Salesbox\Facades;

use Illuminate\Support\Facades\Facade;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SalesboxApi
 * @method static void setAccessToken(string $accessToken)
 * @method static string authenticate(string $token = '')
 *
 * @method static array getAccessToken(array $params = [])
 * @method static array getCategories(array $params = [], array $guzzleOptions = [])
 * @method static array createManyCategories(array $params, array $guzzleOptions = [])
 * @method static array updateManyCategories(array $params, array $guzzleOptions = [])
 * @method static array deleteManyCategories(array $params, array $guzzleOptions = [])
 * @method static array createCategory(array $params = [], array $guzzleOptions = [])
 * @method static array updateCategory(array $params = [], array $guzzleOptions = [])
 * @method static array deleteCategory(array $params = [], array $guzzleOptions = [])
 * @method static array getOffers(array $params = [], array $guzzleOptions = [])
 *
 * @see  \App\Salesbox\SalesboxApi;
 */

class SalesboxApi extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'salesboxapi';
    }
}
