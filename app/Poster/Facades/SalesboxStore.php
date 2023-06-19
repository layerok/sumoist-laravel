<?php

namespace App\Poster\Facades;

use App\Poster\SalesboxCategory;
use App\Poster\SalesboxOffer;
use Illuminate\Support\Facades\Facade;

/**
 * Class SalesboxStore
 * @method static SalesboxCategory[] loadCategories()
 * @method static SalesboxOffer[] loadOffers()
 * @method static SalesboxOffer[] getOffers()
 * @method static SalesboxCategory[] getCategories()
 * @method static SalesboxCategory|null findCategory($externalId)
 * @method static bool categoryExists($externalId)
 * @method static array|null deleteCategory($externalId)
 * @method static array updateManyCategories(array $categories)
 * @method static array createManyCategories(array $categories)
 * @method static SalesboxOffer|null findOffer($externalId)
 * @method static void authenticate()
 * @method static RootStore getRootStore()
 *
 * @see  \App\Poster\Stores\SalesboxStore;
 */

class SalesboxStore extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'salesbox.store';
    }
}
