<?php

use Illuminate\Database\Seeder;

class PaymentStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_statuses')->insert([
            [
                'key' => 'InProcessing',
                'name' => 'В обработке'
            ],
            [
                'key' => 'WaitingAuthComplete',
                'name' => 'Успешный HOLD'
            ],
            [
                'key' => 'Approved',
                'name' => 'Успешный платеж'
            ],
            [
                'key' => 'Pending',
                'name' => 'На проверке Antifraud'
            ],
            [
                'key' => 'Expired',
                'name' => 'Истек срок оплаты'
            ],
            [
                'key' => 'Refunded',
                'name' => 'Возврат'
            ],
            [
                'key' => 'Declined',
                'name' => 'Отклонен'
            ],
            [
                'key' => 'Voided',
                'name' => 'Возрат'
            ],
            [
                'key' => 'RefundInProcessing',
                'name' => 'Возврат в обработке'
            ],
            [
                "key"   => 'NoPaid',
                'name'  => "Не оплачен"
            ]
        ]);
    }
}
