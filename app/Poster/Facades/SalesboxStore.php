<?php

namespace App\Poster\Facades;

use App\Poster\Models\PosterCategory;
use App\Poster\Models\PosterProduct;
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
 * @method static array deleteCategory(SalesboxCategory $salesboxCategory)
 * @method static array updateManyCategories(SalesboxCategory[] $categories)
 * @method static array createManyCategories(SalesboxCategory[] $categories)
 * @method static array createManyOffers(SalesboxOffer[] $offers)
 * @method static array updateManyOffers(SalesboxOffer[] $offers)
 * @method static array deleteManyOffers(SalesboxOffer[] $offers)
 *
 * @method static SalesboxOffer[] updateFromPosterProducts(PosterProduct[] $poster_product)
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
