<?php

namespace App\Salesbox\Facades;

use App\Salesbox\meta\SalesboxApiResponse_meta;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Facade;

/**
 * Class SalesboxApi
 * @method static string authenticate(string $token = '')
 *
 * @method static SalesboxApiResponse_meta getAccessToken(array $params = [])
 * @method static SalesboxApiResponse_meta getCategories(array $params = [], array $guzzleOptions = [])
 * @method static SalesboxApiResponse_meta createManyCategories(array $params, array $guzzleOptions = [])
 * @method static SalesboxApiResponse_meta updateManyCategories(array $params, array $guzzleOptions = [])
 * @method static SalesboxApiResponse_meta deleteManyCategories(array $params, array $guzzleOptions = [])
 * @method static SalesboxApiResponse_meta createCategory(array $params = [], array $guzzleOptions = [])
 * @method static SalesboxApiResponse_meta updateCategory(array $params = [], array $guzzleOptions = [])
 * @method static SalesboxApiResponse_meta deleteCategory(array $params = [], array $guzzleOptions = [])
 * @method static SalesboxApiResponse_meta getOffers(array $params = [], array $guzzleOptions = [])
 * @method static SalesboxApiResponse_meta createManyOffers(array $params = [], array $guzzleOptions = [])
 * @method static SalesboxApiResponse_meta updateManyOffers(array $params = [], array $guzzleOptions = [])
 * @method static SalesboxApiResponse_meta deleteManyOffers(array $params, array $guzzleOptions = [])
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
