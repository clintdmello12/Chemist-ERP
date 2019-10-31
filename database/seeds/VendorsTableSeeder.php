<?php

use Illuminate\Database\Seeder;

class VendorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Default vendors
         * @var array
         */
        $vendors = [
            ['vendor1', 'vendor1@chemist.com', ''],
            ['vendor2', 'vendor2@chemist.com', ''],
            ['vendor3', 'vendor3@chemist.com', ''],
        ];

        foreach ($vendors as $vendor) {
            App\Vendor::create([
                'name' => $vendor[0],
                'email' => $vendor[1],
                'phone' => $vendor[2],
            ]);
        }
    }
}
