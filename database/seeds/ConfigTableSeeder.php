<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Config;

class ConfigTableSeeder extends Seeder
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
                'key' => 'Reverify user phone number after n days',
                'value' => 90,
                'is_link' => 0,
                'sort_order' => 1,
                'is_different_lang' => 0,
                'is_show_hide' => 0
            ],
            [
                'key' => 'Master Password',
                'value' => "",
                'is_link' => 0,
                'sort_order' => 2,
                'is_different_lang' => 0,
                'is_show_hide' => 0
            ],
            [
                'key' => 'signup email',
                'value' => 'gwb9160@nate.com',
                'is_link' => 1,
                'sort_order' => 3,
                'is_different_lang' => 0,
                'is_show_hide' => 0
            ],
            [
                'key' => 'delete account reason',
                'value' => '',
                'is_link' => 1,
                'sort_order' => 4,
                'is_different_lang' => 0,
                'is_show_hide' => 0
            ],
        ];

        foreach ($items as $item) {
            $key = Str::slug($item['key'], '_');
            $planCount = Config::where('key', $key)->count();
            if ($planCount == 0) {
                $plans = Config::firstOrCreate([
                    'key' => $key,
                    'value' => $item['value'],
                    'is_link' => $item['is_link'],
                    'sort_order' => $item['sort_order'],
                    'is_different_lang' => $item['is_different_lang'],
                    'is_show_hide' => $item['is_show_hide'] ?? 0,
                ]);
            }
        }
    }
}
