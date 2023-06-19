<?php

namespace App\Poster\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class RootStore
 * @method static RootStore getSalesboxStore()
 * @method static RootStore getPosterStore()
 *
 * @see  \App\Poster\Stores\PosterStore;
 */

class RootStore extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'root.store';
    }
}
