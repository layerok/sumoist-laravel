<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Contracts\CategoryContract;
use Illuminate\Support\Facades\DB;
use App\Libraries\Poster;

class CategoriesController extends Controller
{

    protected $categoryRepository;

    public function __construct(CategoryContract $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index($spotSlug)
    {
        $spots = DB::table('spots')->get();

        foreach($spots as $spot) {
            $poster = new Poster($spot->poster_token);
            $data = json_decode($poster->query('menu.getProducts'), true);
            dd($data['response'][0]);
        }

        $category = $this->categoryRepository->listCategoriesBySpotSlug($spotSlug);
        if(!empty($category)){
            return $category;
        }else{
            abort(404);
        }

    }

    public function show($spotSlug, $slug)
    {
        $category = $this->categoryRepository->findBySlugAndSpot($slug, $spotSlug);
        //dd($category);

        foreach($category['products'] as $key => $value) {

            $product = Product::with('ingredients')->where('id', $value['id'])->first();

            $filtered = $product['ingredients']->filter(function ($value, $key) {
                return $value['status'] == 1;
            });

            //dd($ingredients['ingredients']);
            $ingredients_string = $filtered->implode( 'name' , ', ');
            $category['products'][$key]->ingredients = $ingredients_string;

            $image = DB::table('product_images')->where('product_id', $value['id'])->value('full');
            $category['products'][$key]->image = $image;
            if($category['products'][$key]['status'] == 0) {
                unset($category['products'][$key]);
            }
        }
        return $category;



    }

}
