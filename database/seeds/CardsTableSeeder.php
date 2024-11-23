<?php

use Illuminate\Database\Seeder;
use App\Models\Cards;

class CardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'start' => 1,
            'end' => 19,
            'card_number' => 1
        ];
        Cards::create($data);

        $start = 1;
        $end = 400;
        $step = 20;
        $values = range($start, $end);
        $rows = array_chunk($values,$step);
        $i = 2;
        foreach($rows as $row) {
            $insertData = [
                'start' => end($row),
                'end' => (end($row)+19),
                'card_number' => $i++
            ];
            Cards::create($insertData);
        }
    }
}
