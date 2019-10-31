<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ManufacturersTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(VendorsTableSeeder::class);
        $this->call(InventoriesTableSeeder::class);
    }
}
