<?php

namespace App\Poster\Facades;

use App\Poster\Models\SalesboxCategory;
use App\Poster\Models\SalesboxOffer;
use Illuminate\Support\Facades\Facade;

/**
 * Class SalesboxStore
 * @method static SalesboxCategory[] loadCategories()
 * @method static SalesboxOffer[] loadOffers()
 * @method static SalesboxOffer[] getOffers()
 * @method static SalesboxOffer|SalesboxOffer[]|null findOffer(string|int|array $externalId)
 * @method static bool offerExists($externalId)
 * @method static SalesboxCategory[] getCategories()
 * @method static SalesboxCategory|SalesboxCategory[]|null findCategory(string|int|array $externalId)
 * @method static bool categoryExists(string|int $externalId)
 * @method static array|null deleteCategory(string|int $externalId)
 * @method static array updateManyCategories(array $categories)
 * @method static array createManyCategories(array $categories)

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
