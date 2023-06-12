<?php

use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use App\Libraries\Poster;

class AttributeValuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sizes = ['small', 'medium', 'large'];
        $colors = ['black', 'blue', 'red', 'orange'];

        foreach ($sizes as $size)
        {
            AttributeValue::create([
                'attribute_id'      =>  1,
                'value'             =>  $size,
                'price'             =>  null,
            ]);
        }

        foreach ($colors as $color)
        {
            AttributeValue::create([
                'attribute_id'      =>  2,
                'value'             =>  $color,
                'price'             =>  null,
            ]);
        }

        $spots = DB::table('spots')->get();
        foreach($spots as $spot){

            $poster = new Poster($spot->poster_token);
            $data = json_decode($poster->query('menu.getIngredients'), true);

            foreach($data["response"] as $value){
                AttributeValue::create([
                    'attribute_id'  => 3,
                    'poster_id'     => $value['ingredient_id'],
                    'value'         => $value['ingredient_name']
                ]);
            }
        }
    }
}
