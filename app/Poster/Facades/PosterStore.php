<?php

namespace App\Poster\Facades;

use App\Poster\PosterCategory;
use App\Poster\PosterProduct;
use App\Poster\SalesboxCategory;
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
 *
 * @see  \App\Poster\Stores\PosterStore;
 */

class PosterStore extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'poster.store';
    }
}
