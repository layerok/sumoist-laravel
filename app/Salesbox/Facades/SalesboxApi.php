<?php

namespace App\Salesbox\Facades;

use App\Salesbox\meta\SalesboxApiResponse_meta;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Facade;

/**
 * Class SalesboxApi
 *
 * @method static array getAccessToken(array $params = [])
 * @method static void setAccessToken(string|null $token)
 * @method static array getCategories(array $params = [], array $guzzleOptions = [])
 * @method static array createManyCategories(array $params, array $guzzleOptions = [])
 * @method static array updateManyCategories(array $params, array $guzzleOptions = [])
 * @method static array deleteManyCategories(array $params, array $guzzleOptions = [])
 * @method static array createCategory(array $params = [], array $guzzleOptions = [])
 * @method static array updateCategory(array $params = [], array $guzzleOptions = [])
 * @method static array deleteCategory(array $params = [], array $guzzleOptions = [])
 * @method static array getOffers(array $params = [], array $guzzleOptions = [])
 * @method static array createManyOffers(array $params = [], array $guzzleOptions = [])
 * @method static array updateManyOffers(array $params = [], array $guzzleOptions = [])
 * @method static array deleteManyOffers(array $params, array $guzzleOptions = [])
 * @method static array getOrderById(string $id, array $guzzleOptions = [])
 *
 * @method static HandlerStack getGuzzleHandler()
 *
 * @see  \App\Salesbox\SalesboxApi;
 */

class SalesboxApi extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'salesboxapi';
    }
}
