<?php

namespace App\Poster\Facades;

use App\Poster\Models\PosterCategory;
use App\Poster\Models\PosterProduct;
use App\Poster\Models\SalesboxCategory;
use App\Poster\Models\SalesboxOfferV4;
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
 * @method static array createManyOffers(SalesboxOfferV4[] $offers)
 * @method static array updateManyOffers(SalesboxOfferV4[] $offers)
 * @method static array deleteManyOffers(SalesboxOfferV4[] $offers)
 *
 * @method static SalesboxOfferV4[] updateFromPosterProducts(PosterProduct[] $poster_product)
 * @method static SalesboxCategory[] updateFromPosterCategories(PosterCategory[] $poster_category)
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
