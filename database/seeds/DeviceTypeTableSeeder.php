<?php

use Illuminate\Database\Seeder;
use App\Models\DeviceTypes;

class DeviceTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = ['Ios', 'Android', 'Web'];

        foreach($items as $item) {
            DeviceTypes::firstOrCreate(['name' => $item]);
        }
    }
}
