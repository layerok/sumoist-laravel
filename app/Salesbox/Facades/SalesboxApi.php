<?php

namespace App\Salesbox\Facades;

use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Facade;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SalesboxApi
 * @method static ResponseInterface getToken()
 * @method static ResponseInterface getCategories(array $guzzleOptions = [])
 * @method static ResponseInterface createManyCategories(array $categories, array $guzzleOptions = [])
 * @method static ResponseInterface updateManyCategories(array $categories, array $guzzleOptions = [])
 * @method static ResponseInterface deleteManyCategories(array $categories, array $guzzleOptions = [], $recursively = false)
 * @method static ResponseInterface getCategory(array $guzzleOptions = [])
 * @method static ResponseInterface createCategory(array $category, array $guzzleOptions = [])
 * @method static ResponseInterface updateCategory(array $category, array $guzzleOptions = [])
 * @method static ResponseInterface deleteCategory(array $category, array $guzzleOptions = [], $recursively = false)

 * @method static HandlerStack getGuzzleHandler()
 * @method static void setHeaders(array $headers = [])
 *
 * @see  \App\Salesbox\SalesboxApi;
 */

class SalesboxApi extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'salesboxapi';
    }
}
