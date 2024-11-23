<?php

use  App\Models\TimeZone;
use Illuminate\Database\Seeder;


class TimezoneTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = env('ASSET_URL'). "/data/time_zones.json";
        $JSON_timezones = file_get_contents($path);
        $JSON_timezones = json_decode($JSON_timezones,true);
        foreach ($JSON_timezones as $timezone) {
           TimeZone::create([
                'name'      => ((isset($timezone['name'])) ? $timezone['name'] : null),
                'abbr'      => ((isset($timezone['abbr'])) ? $timezone['abbr'] : null),
                'offset'    => ((isset($timezone['offset'])) ? $timezone['offset'] : null),
                'isdst'     => ((isset($timezone['isdst'])) ? $timezone['isdst'] : null),
                'text'      => ((isset($timezone['text'])) ? $timezone['text'] : null),
                'utc'       => ((isset($timezone['utc'])) ? $timezone['utc'] : null),
            ]);
        }
    }
}
