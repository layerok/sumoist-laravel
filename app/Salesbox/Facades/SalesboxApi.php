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
 * @method static array deleteManyCategories(array $params, array $guzzleOptions = [])
 * @method static SalesboxApiResponse_meta createCategory(array $params = [], array $guzzleOptions = [])
 * @method static SalesboxApiResponse_meta updateCategory(array $params = [], array $guzzleOptions = [])
 * @method static array deleteCategory(array $params = [], array $guzzleOptions = [])
 * @method static array getOffers(array $params = [], array $guzzleOptions = [])
 * @method static array createManyOffers(array $params = [], array $guzzleOptions = [])
 * @method static array updateManyOffers(array $params = [], array $guzzleOptions = [])
 * @method static array deleteManyOffers(array $params, array $guzzleOptions = [])
 * @method static array getCategoryByExternalId(string|int $id)
 * @method static array deleteCategoryByExternalId(string|int $id, $recursively = false)
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
