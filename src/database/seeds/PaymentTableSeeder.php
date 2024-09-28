<?php




use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PaymentTableSeeder extends Seeder
{


    public function run()
    {

        DB::table('payment')->insert([
            ['name' => 'Картой'],
            ['name' => 'Наличными'],
            ['name' => 'WayForPay']
        ]);

    }



}
