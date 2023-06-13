<?php

namespace App\Salesbox\Facades;

use Illuminate\Support\Facades\Facade;
use Psr\Http\Message\ResponseInterface;

/**
 * Class WayForPay
 * @package Maksa988\WayForPay\Facades
 * @method static ResponseInterface getToken()
 * @method static void setAccessToken($accessToken)
 * @method static ResponseInterface getCategories()
 * @method static ResponseInterface createManyCategories(array $categories)
 * @method static ResponseInterface updateManyCategories(array $categories)
 * @method static ResponseInterface deleteManyCategories(array $categories)
 * @method static ResponseInterface getCategory()
 * @method static ResponseInterface createCategory(array $category)
 * @method static ResponseInterface updateCategory(array $category)
 * @method static ResponseInterface deleteCategory(array $category)
 *
 * @see  \App\Salesbox\SalesboxApi;
 */

class SalesboxApi extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'salesboxapi';
    }
}
