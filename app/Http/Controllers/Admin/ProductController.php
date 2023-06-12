<?php

namespace App\Http\Controllers\Admin;

use App\Libraries\Poster;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use App\Contracts\CategoryContract;
use App\Contracts\ProductContract;
use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreProductFormRequest;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;


class ProductController extends BaseController
{


    protected $categoryRepository;

    protected $productRepository;

    public function __construct(
        CategoryContract $categoryRepository,
        ProductContract $productRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        $products = Product::with('categories')->get();
        $this->setPageTitle('Продукты', 'Список продуктов');
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = $this->categoryRepository->listCategories('name', 'asc');

        $this->setPageTitle('Продукты', 'Создать продукт');
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductFormRequest $request)
    {
        $params = $request->except('_token');

        $product = $this->productRepository->createProduct($params);

        if (!$product) {
            return $this->responseRedirectBack('Error occurred while creating product.', 'error', true, true);
        }
        return $this->responseRedirect('admin.products.index', 'Product added successfully' ,'success',false, false);
    }

    public function edit($id)
    {
        $product = $this->productRepository->findProductById($id);
        $categories = $this->categoryRepository->listCategories('name', 'asc');


        $this->setPageTitle('Продукты', 'Редактировать продукт');
        return view('admin.products.edit', compact('categories', 'product'));
    }

    public function update(StoreProductFormRequest $request)
    {
        $params = $request->except('_token');

        $product = $this->productRepository->updateProduct($params);

        if (!$product) {
            return $this->responseRedirectBack('Error occurred while updating product.', 'error', true, true);
        }

        if($params['action'] == "save" ){
            return $this->responseRedirectBack('Продукт успешно обновлен', 'success', true, true);
        }
        return $this->responseRedirect('admin.products.index', 'Product updated successfully' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $product = $this->productRepository->deleteProduct($id);

        if (!$product) {
            return $this->responseRedirectBack('Error occurred while deleting product.', 'error', true, true);
        }
        return $this->responseRedirect('admin.products.index', 'Product deleted successfully' ,'success',false, false);
    }

    public function syncPhotos(){
        $poster = new Poster(config('poster.access_token'));
        $data = json_decode($poster->query('menu.getProducts'), true);


        foreach($data['response'] as $product){
            if(isset($product['product_id'])){

                if(!empty($product['photo'])) {
                    Product::where('poster_id', '=', $product['product_id'])->update([
                        "image" => config('poster.url') . $product["photo"]
                    ]);
                }else{
                    Product::where('poster_id', '=', $product['product_id'])->update([
                        "image" => null
                    ]);
                }

            }

        }

        return $this->responseRedirect('admin.products.index', 'Фотографии синхронизированы успешно' ,'success',false, false);
    }

    public function syncPrices(){
        function delEndind($str){
            if(!empty($str)){
                $sub_str = substr($str, 0, strlen($str)-2);
                return $sub_str;
            }

            return 0;
        }

        $poster = new Poster(config('poster.access_token'));
        $data = json_decode($poster->query('menu.getProducts'), true);

        //dd($data);
        $arr_product_ids = [];
        foreach($data['response'] as $product){


            $arr_product_ids[] = $product['product_id'];
            if (isset($product['modifications'])) {

                foreach ($product['modifications'] as $value1) {

                    $product_id = Product::where("poster_id", "=", $product['product_id'])->value('id');

                    AttributeValue::where("poster_id", "=", $value1['modificator_id'])
                        ->first()
                        ->productAttributes()
                        ->where("product_attributes.id", "=", $product_id)
                        ->update([
                            'price'         => delEndind($value1['spots'][0]['price']),
                        ]);
                }
            }
            else {
                $record['price'] = delEndind($product['price'][1]);
                Product::where("poster_id", "=", $product['product_id'])->update([
                    'price' => $record['price'],
                    'weight' => $product['out'],
//                    'hidden' => $product['spots'][0]['visible'] == 1 ? 0 : 1
                ]);

            }
        }
        //dd($arr_product_ids);
        Product::whereNotIn('poster_id', $arr_product_ids)->delete();

        return $this->responseRedirect('admin.products.index', 'Цены синхронизированы успешно' ,'success',false, false);
    }

    public function syncProducts(){
        $products_ids = [];
        $category_ids = [];

        function cleanup($table_name)
        {
            DB::statement("SET @count = 0;");
            DB::statement("UPDATE `$table_name` SET `$table_name`.`id` = @count:= @count + 1;");
            DB::statement("ALTER TABLE `$table_name` AUTO_INCREMENT = 1;");
        }

        function match($pattern_array, $value) {
            $matches = false;
            foreach ($pattern_array as $pattern)
            {
                if (preg_match($pattern, $value))
                {
                    $matches = true;
                }
            }

            return $matches;
        }

        function delEndind($str){
            if(!empty($str)){
                $sub_str = substr($str, 0, strlen($str)-2);
                return $sub_str;
            }

            return 0;
        }


        function skipIngredient($ingredient) {
            $skipIngredients = [
                'Бутылка соев соус \d*мл',
                'Набор ролл',
                'Моющее д\/посуды л',
                'Бокс д\/суши',
                'Губка д\/посуды',
                'Палочки суши',
                'Коробка лапша',
                'сливки \d*%',
                'Масло растительное',
                'Салфетка целлюлоза',
                'Дезинфектор АХД 2000',
                'Держатели д\/палочек',
                'Ершик д\/посуды уп',
                'Заправка для риса',
                'кофе зерна',
                'Фанни сыр'
            ];

            $patterns = [];

            foreach($skipIngredients as $skip) {
                $patterns[] = '/' . $skip . '/';
            }
            return match($patterns, $ingredient['ingredient_name']);
        }

/*        function createIngredient($ingredient) {
            $replacers = [
                '^Ролл',
                '^ролл',
                'ПФ$',
                'пф$',
                'Пф$',
                'филе на шкуре',
                '31\/40',
                '16\/20$'
            ];

            $pattern = '/' . implode('|',$replacers) .'/';

            $ingredient_name = preg_replace($pattern,'',$ingredient['ingredient_name']);
            // Создаю ингридиент и привязываю его к аттрибуты "Ингридиент"
            if(AttributeValue::where('poster_id', '=', $ingredient['ingredient_id'])->exists()){
                return AttributeValue::where('poster_id', '=', $ingredient['ingredient_id'])->first();
            }else{
                return AttributeValue::insertGetId([
                    'attribute_id' => 3,// 3 - id аттрибута "Ингридиент"
                    'value'        => $ingredient_name,
                    'poster_id'    => $ingredient['ingredient_id']
                ]);
            }
        }*/



        Schema::disableForeignKeyConstraints();

        AttributeValue::whereNotNull('id')->delete();

        cleanup('attribute_values');

        $poster = new Poster(config('poster.access_token'));


        $categories = json_decode($poster->query('menu.getCategories'), true);

        foreach($categories['response'] as $value){
            $category_ids[] = $value['category_id'];
            Category::updateOrCreate(
                ['poster_id'     => $value['category_id']],
                [
                    'name' => $value['category_name'],
                    'sort_order' => $value['sort_order']
                ]
            );
        }

/*        $attributes = json_decode($poster->query('menu.getIngredients'), true);

        foreach($attributes["response"] as $value){

            $skip = skipIngredient($value);

            if(!$skip) {
                createIngredient($value);
            }
        }*/


        // products
        $data = json_decode($poster->query('menu.getProducts'), true);


        foreach($data['response'] as $value) {

            $products_ids[] = $value['product_id'];
/*            AttributeValue::create([
                'attribute_id'  => 3,
                'value'         => $value['product_name'],
                'poster_id'     => $value['product_id'],
            ]);*/

            $record =  [
                'poster_id'     => $value['product_id'],
                'name'          => $value['product_name'],
                'weight'        => $value['out'],
                'image'         => $value['photo'] ? config('poster.url') . $value['photo'] : null,
                'unit'          => 'г',
                'sort_order'    => $value['sort_order']
            ];


            if (isset($value['modifications'])) {
                $attribute_id = Attribute::insertGetId([
                    'name'          => $value['product_name'] . "_modificator",
                    'code'          => str_slug($value['product_name'] . "_modificator" ),
                    'frontend_type' => 'radio',
                    'is_filterable' => 1
                ]);


                $product = Product::updateOrCreate(
                    ['poster_id' => $value['product_id']],
                    $record
                );

                $category = Category::wherePosterId($value['menu_category_id'])->first();

                if(!empty($category)){
                    $product->categories()->attach($category['id']);
                }



                foreach ($value['modifications'] as $value1) {

                    $attribute_value_id = AttributeValue::insertGetId([
                        'attribute_id' => $attribute_id,
                        'value'        => $value1['modificator_name'],
                        'poster_id'    => $value1['modificator_id'],
                        'price'        => delEndind($value1['spots'][0]['price'])
                    ]);

                    $product_attribute = ProductAttribute::create([
                        'price'         => delEndind($value1['spots'][0]['price']),
                        'product_id'    => $product->id
                    ]);

                    $product_attribute->attributeValues()->attach($attribute_value_id);
                }

            } else {
                $record['price'] = delEndind($value['price'][1]);
                $product = Product::updateOrCreate(
                    ['poster_id' => $value['product_id']],
                    $record
                );
                $category = Category::wherePosterId($value['menu_category_id'])->first();
                if(!empty($category)){
                    $product->categories()->sync([$category['id']]);
                }

                $description = '';

                if(isset($value['ingredients'])){

                    // Перебираю массив ингредиентов
                    foreach($value['ingredients'] as $ingredient){
                        if($description === '') {
                            $description = $ingredient['ingredient_name'];
                        } else {
                            $description = ', ' . $ingredient['ingredient_name'];
                        }


                        /*$skip = skipIngredient($ingredient);

                        if(!$skip) {
                            $attribute_value_id = createIngredient($ingredient);

                            $such_product_attribute = AttributeValue::where("poster_id", "=", $ingredient['ingredient_id'])
                                ->first()
                                ->productAttributes()
                                ->where('product_id', '=', $product->id)
                                ->first();

                            if(!isset($such_product_attribute)){
                                // Создаю аттрибут для продукта
                                $product_attribute = ProductAttribute::create([
                                    'product_id' =>  $product->id
                                ]);

                                // Привязываю созданные выше аттрибут и ингридиет,
                                $product_attribute->attributeValues()->sync($attribute_value_id);
                            }
                        }*/

                    }

                }
                if(is_null($product->description)) {
                    $product->description = $description;
                    $product->save();
                }




            }
        }

        Product::whereNotIn('poster_id', $products_ids)->delete();
        Category::whereNotIn('poster_id', $category_ids)->delete();




        return $this->responseRedirect('admin.products.index', 'Товары синхронизированы успешно' ,'success',false, false);
    }

    public function syncIngredients(){
        $arr_ingredients_ids = [];

        $poster = new Poster(config('poster.access_token'));
        $data = json_decode($poster->query('menu.getProducts'), true);

        $arr_ingredients_ids = [];
        foreach($data['response'] as $value) {

            if (isset($value['modifications'])) {


            } else {



                if(isset($value['ingredients'])){

                    // Перебираю массив ингредиентов
                    foreach($value['ingredients'] as $ingredient){

                        $arr_ingredients_ids[] = $ingredient['ingredient_id'];
                        // Создаю ингридиент и привязываю его к аттрибуты "Ингридиент"
                        if(AttributeValue::where('poster_id', '=', $ingredient['ingredient_id'])->exists()){
                            $attribute_value = AttributeValue::where('poster_id', '=', $ingredient['ingredient_id'])->first();
                            $attribute_value_id = $attribute_value->id;
                        }else{
                            $attribute_value = AttributeValue::updateOrInsert([
                                'poster_id'    => $ingredient['ingredient_id'],
                                'attribute_id' => 3,// 3 - id аттрибута "Ингридиент"
                                'value'        => $ingredient['ingredient_name'],

                            ]);
                            $attribute_value_id = $attribute_value->first()->value('id');

                        }



                        // Создаю аттрибут для продукта
                        $product_attribute = ProductAttribute::updateOrInsert([
                            'product_id' =>  Product::where('poster_id', '=', $value['product_id'])->value('id')
                        ])->first();



                        $product_attribute->attributeValues()->attach($attribute_value_id);
                        //exit();

                    }
                    // Привязываю созданные выше аттрибут и ингридиет,



                }




            }

        }


        AttributeValue::whereNotIn('poster_id', $arr_ingredients_ids)->delete();
        return $this->responseRedirect('admin.products.index', 'Товары синхронизированы успешно' ,'success',false, false);
    }



}
