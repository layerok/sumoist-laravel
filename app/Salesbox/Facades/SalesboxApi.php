<?php

namespace App\Salesbox\Facades;

use Illuminate\Support\Facades\Facade;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SalesboxApi
 * @method static void setAccessToken(string $accessToken)
 * @method static string authenticate(string $token = '')
 *
 * @method static array getAccessToken()
 * @method static array getCategories(array $guzzleOptions = [])
 * @method static array createManyCategories(array $categories, array $guzzleOptions = [])
 * @method static array updateManyCategories(array $categories, array $guzzleOptions = [])
 * @method static array deleteManyCategories(array $categories, array $guzzleOptions = [], $recursively = false)
 * @method static array createCategory(array $category, array $guzzleOptions = [])
 * @method static array updateCategory(array $category, array $guzzleOptions = [])
 * @method static array deleteCategory(array $category, array $guzzleOptions = [], $recursively = false)
 *
 * @see  \App\Salesbox\SalesboxApi;
 */

class SalesboxApi extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'salesboxapi';
    }
}
