<?php

namespace App\Salesbox\Facades;

use App\Salesbox\Models\SalesboxCategory;
use App\Salesbox\Models\SalesboxOfferV4;
use App\Salesbox\Models\SalesboxOrder;
use Illuminate\Support\Facades\Facade;

/**
 * Class SalesboxStore
 * @method static SalesboxCategory[] loadCategories()
 * @method static SalesboxOfferV4[] loadOffers()
 * @method static SalesboxOfferV4[] getOffers()
 * @method static SalesboxOfferV4|SalesboxOfferV4[]|null findOfferByExternalId(string|int|array $externalId, string|int|null $modificatorId = null)
 * @method static bool offerExistsWithExternalId($externalId)
 * @method static SalesboxCategory[] getCategories()
 * @method static SalesboxCategory|SalesboxCategory[]|null findCategoryByExternalId(string|int|array $externalId)
 * @method static bool categoryExistsWithExternalId(string|int $externalId)
 * @method static array deleteCategory(SalesboxCategory $salesboxCategory)
 * @method static array updateManyCategories(SalesboxCategory[] $categories)
 * @method static array createManyCategories(SalesboxCategory[] $categories)
 * @method static array deleteManyCategories(SalesboxCategory[] $categories)
 * @method static array createManyOffers(SalesboxOfferV4[] $offers)
 * @method static array updateManyOffers(SalesboxOfferV4[] $offers)
 * @method static array deleteManyOffers(SalesboxOfferV4[] $offers)
 * @method static SalesboxOrder|null getOrderById(string $id)
 *
 * @method static bool isCategoriesLoaded()
 * @method static bool isOffersLoaded()
 *
 * @method static void authenticate()
 *
 * @see  \App\Salesbox\Stores\SalesboxStore;
 */

class SalesboxStore extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'salesbox.store';
    }
}
