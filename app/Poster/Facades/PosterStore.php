<?php

namespace App\Poster\Facades;

use App\Poster\PosterCategory;
use App\Poster\PosterProduct;
use Illuminate\Support\Facades\Facade;

/**
 * Class PosterStore
 * @method static PosterCategory[] loadCategories()
 * @method static PosterProduct[] loadProducts()
 * @method static PosterCategory|null findCategory($posterId)
 * @method static PosterProduct|null findProduct($posterId)
 * @method static PosterCategory[] getCategories()
 * @method static PosterProduct[] getProducts()
 * @method static RootStore getRootStore()
 *
 * @see  \App\Poster\Stores\PosterStore;
 */

class PosterStore extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'poster.store';
    }
}
