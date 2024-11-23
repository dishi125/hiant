<?php

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = ['Active', 'Deactive','Pending','Expired','Hidden','Unhide','Future'];

        foreach($items as $item) {
            Status::firstOrCreate(['name' => $item]);
        }
    }
}
