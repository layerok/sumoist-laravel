<?php

namespace App\Poster\Facades;

use App\Poster\Models\PosterCategory;
use App\Poster\Models\PosterProduct;
use App\Poster\Models\SalesboxCategory;
use App\Poster\Models\SalesboxOffer;
use Illuminate\Support\Facades\Facade;

/**
 * Class PosterStore
 * @method static PosterCategory[] loadCategories()
 * @method static PosterProduct[] loadProducts()
 * @method static PosterCategory[]|PosterCategory|null findCategory(array|string|int $posterId)
 * @method static bool categoryExists(string|int $posterId)
 * @method static PosterProduct|PosterProduct[]|null findProduct(array|string|int $posterId)
 * @method static PosterCategory[] getCategories()
 * @method static PosterProduct[] getProducts()
 * @method static RootStore getRootStore()
 * @method static SalesboxCategory[] asSalesboxCategories(PosterCategory[] $poster_categories)
 * @method static SalesboxOffer[] asSalesboxOffers(PosterProduct[] $poster_products)
 *
 * @see  \App\Poster\Stores\PosterStore;
 */

class PosterStore extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'poster.store';
    }
}
