<?php

namespace App\Http\Controllers\Site;

use Facuz\Theme\Facades\Theme;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contracts\CategoryContract;
use App\Libraries\Telegram;
use Cart;
use App\Libraries\Poster;
use poster\src\PosterApi;

class IndexController extends Controller
{
    private $catalog_sort;




    public function index()
    {
        $this->setCatalogSort();

        $searched_word = $_POST['word'] ?? '';

        $products = \App\Models\Product::with(['attributes.attributeValues', 'images'])
            ->select('products.*')
            ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id' )
            ->leftJoin('categories', 'categories.id', '=', 'product_categories.category_id' )
            ->where([
                ['products.hidden', '=', 0],
                ['categories.hidden', '=', 0]
            ])
            ->when($this->catalog_sort, function ($query, $sortBy) {
               return $query->orderByRaw($sortBy);
            })->where('products.name', 'like', "%{$searched_word}%" )->get();

        return view('theme::site.pages.homepage', compact('products'));
    }

    private function setCatalogSort()
    {
        if (isset($_POST['catalog_sort_type'])) {
            $catalog_sort_type = $_POST['catalog_sort_type'];

        } else if (isset($_SESSION['catalog_sort_type'])) {
            $catalog_sort_type = $_SESSION['catalog_sort_type'];

        } else {
            $catalog_sort_type = 1;
        }

        $_SESSION['catalog_sort_type'] = $catalog_sort_type;

        switch ($catalog_sort_type) {
            case 1:
                // по умолчанию
                $this->catalog_sort = "categories.sort_order ASC, products.sort_order ASC, products.id DESC";
                break;
            case 2:
                // Цена (возрастание)
                $this->catalog_sort = 'price DESC';
                break;
            case 3:
                // Цена (убывание)
                $this->catalog_sort = 'price ASC';
                break;
            case 4:
                // Сначала новые
                $this->catalog_sort = 'categories.sort_order ASC, products.id ASC ';
                break;
            case 5:
                // Сначала старые
                $this->catalog_sort = 'categories.sort_order ASC, products.id DESC';
                break;
            default:
                // Сортировка не применяется
                $this->catalog_sort = null;
        }


    }



}
