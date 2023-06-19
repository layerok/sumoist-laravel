<?php

namespace App\Poster\Facades;

use App\Poster\meta\PosterProduct_meta;
use App\Poster\PosterCategory;
use Illuminate\Support\Facades\Facade;

/**
 * Class PosterStore
 * @method static PosterCategory[] loadCategories()
 * @method static PosterProduct_meta[] loadProducts()
 * @method static PosterCategory|null findCategory($posterId)
 * @method static PosterProduct_meta|null findProduct($posterId)
 * @method static PosterCategory[] getCategories()
 * @method static PosterProduct_meta[] getProducts()
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
