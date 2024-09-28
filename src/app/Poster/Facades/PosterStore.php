<?php

namespace App\Poster\Facades;

use App\Poster\Models\PosterCategory;
use App\Poster\Models\PosterProduct;
use App\Poster\Models\PosterProductModification;
use Illuminate\Support\Facades\Facade;

/**
 * Class PosterStore
 * @method static PosterCategory[] loadCategories()
 * @method static PosterProduct[] loadProducts()
 * @method static PosterCategory[]|PosterCategory|null findCategory(array|string|int $poster_id)
 * @method static bool categoryExists(string|int $poster_id)
 * @method static PosterProduct|PosterProduct[]|null findProduct(array|string|int $poster_id)
 * @method static PosterProductModification|null findProductModification(string|int $poster_id, string|int $modificator_id)
 * @method static PosterProduct[] findProductsWithModifications(array $poster_ids)
 * @method static PosterProduct[] findProductsWithoutModifications(array $poster_ids)
 * @method static PosterProduct[] findProductsWithModificationGroups(array $poster_ids)
 * @method static PosterProduct[] findProductsWithoutModificationGroups(array $poster_ids)
 * @method static bool productExists(string|int $poster_id)
 * @method static PosterCategory[] getCategories()
 * @method static PosterProduct[] getProducts()
 * @method static PosterCategory[] getCategoryParents(PosterCategory $category)
 * @method static void init();
 *
 * @method static bool isCategoriesLoaded()
 * @method static bool isProductsLoaded()
 *
 * @see  \App\Poster\Stores\PosterStore;
 */

class PosterStore extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'poster.store';
    }
}
