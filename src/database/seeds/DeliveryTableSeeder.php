<?php



use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;




class DeliveryTableSeeder extends Seeder
{


    public function run()
    {

        DB::table('delivery')->insert([
            ['name' => 'Самовывоз'],
            ['name' => 'Доставка']
        ]);

    }

}
