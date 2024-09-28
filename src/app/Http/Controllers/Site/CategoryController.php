<?php

namespace App\Http\Controllers\Site;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contracts\CategoryContract;

class CategoryMock {
    public $name;
    public $products;

    public function __construct($name, $products) {
        $this->name = $name;
        $this->products = $products;
    }
}

class CategoryController extends Controller
{
    protected $categoryRepository;

    private $catalog_sort;

    public function __construct(CategoryContract $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function show($slug)
    {
//        $category = $this->categoryRepository->findBySlug($slug);

        $this->setCatalogSort();

        $category = Category::with(['products' =>function ($query) {
            $query->with('images')->where('hidden', '=', 0)->when($this->catalog_sort, function ($query, $sortBy) {
                return $query->orderByRaw($sortBy);
            });
        }])->where('slug', $slug)
            ->first();


        if(!$category) {
            $category = new CategoryMock("Категория не найдена", []);
        }


        return view('theme::site.pages.category', compact('category', 'slug'));
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
                $this->catalog_sort = "sort_order ASC, id DESC";
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
                $this->catalog_sort = 'id ASC';
                break;
            case 5:
                // Сначала старые
                $this->catalog_sort = 'id DESC';
                break;
            default:
                // Сортировка не применяется
                $this->catalog_sort = null;
        }


    }



}
