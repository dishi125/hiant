<?php

use Illuminate\Database\Seeder;

class AdvanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'title' => 'Group chat icon',
                'key' => 'group_chat_icon',
                'is_show' => 1,
            ],
        ];

        foreach ($items as $item) {
            \App\Models\Advance::firstOrCreate([
                'title' => $item['title'],
                'is_show' => $item['is_show'],
                'key' => $item['key']
            ]);
        }
    }
}
