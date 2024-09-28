<?php

use App\Libraries\Poster;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Не удалять
        Category::create([
            'name' => 'Корневая',
            'parent_id' => 0
        ]);

        $spots = DB::table('spots')->get();

        foreach($spots as $spot){
            $poster = new Poster($spot->poster_token);
            $categories = json_decode($poster->query('menu.getCategories'), true);

            foreach($categories['response'] as $value){
                Category::create([
                    'name'          => $value['category_name'],
                    'poster_id'     => $value['category_id'],
                    'sort_order'    => $value['sort_order']
                ]);
            }
        }




        //factory('App\Models\Category', 10)->create();
    }
}
