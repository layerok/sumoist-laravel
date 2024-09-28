<?php

use Illuminate\Database\Seeder;
use App\Models\Spot;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SpotsTableSeeder::class);

        $this->call(AdminsTableSeeder::class);

        $this->call(AttributesTableSeeder::class);
        $this->call(AttributeValuesTableSeeder::class);

        $this->call(SettingsTableSeeder::class);

        $this->call(CategoriesTableSeeder::class);
        $this->call(ProductsTableSeeder::class);

        $this->call(DeliveryTableSeeder::class);
        $this->call(PaymentTableSeeder::class);
        $this->call(PaymentStatusTableSeeder::class);

        $this->call(PagesTableSeeder::class);


    }
}
