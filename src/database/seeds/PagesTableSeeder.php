<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use \App\Models\Page;
use \Illuminate\Support\Str;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Page::insert([
            [
                'name' => 'Доставка и оплата',
                'slug' => 'dostavka',
                'hidden' => 0,
                'content' => '<h3>Доставка</h3>
            <p>Мы осуществляем доставку по городу Черноморск и прилегающие населенные пункты (Александровка, Молодежное, Великодолинское)</p>
            <p>Прием заказов на доставку осуществляется с 10:00 до 22:30 ежедневно</p>
            <br>
            <h3>Условия бесплатной доставки</h3>
            <p>При заказе на сумму от 250 грн. доставка бесплатная</p>
            <p>Стоимость доставки на сумму до 250 грн. составляет 25 грн.</p>',
            ],
            [
                'name' => 'Спасибо',
                'slug' => 'thankyou',
                'hidden' => 1,
                'content' => ''
            ],

        ]);
    }
}
