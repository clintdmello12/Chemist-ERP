<?php

use Illuminate\Database\Seeder;

class ManufacturersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Default manufacturers
         * @var array
         */
        $manufacturers = [
            ['ABBOTT INDIA LTD.(HEPATIC CARE)'],
            ['ABBOTT LABORATORIES LTD.(ONCO)'],
            ['ABBOTT LABORATORIES LTD.(REGUL)'],
            ['CIPLA ONCO'],
            ['JOHNSON & JOHNSON LTD.(IMMUNO)'],
            ['JOHNSON & JOHNSON LTD.(ONCOLOGY)'],
            ['LG LIFE SCIENCES PVT.LTD.'],
            ['LIFECARE INNOVATION PVT.LTD.'],
            ['NOVARTIS INDIA LTD.(ONCOLOGY)'],
            ['RANBAXY'],
            ['RELIANCE LIFE SCIENCES(ONCOLOG)'],
            ['RELIANCE RELINOVA DIVISION'],
        ];

        foreach ($manufacturers as $manufacturer) {
            App\Manufacturer::create([
                'name' => $manufacturer[0],
            ]);
        }
    }
}
