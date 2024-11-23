<?php

use Illuminate\Database\Seeder;

class NewsWebsiteTableSeeder extends Seeder
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
                'website_name' => '이데일리',
                'type' => 'edaily',
                'order' => 1
            ],
            [
                'website_name' => '이투데이',
                'type' => 'etoday',
                'order' => 2
            ],
            [
                'website_name' => '파이낸셜뉴스',
                'type' => 'fnnews',
                'order' => 3
            ],
            [
                'website_name' => '한국경제',
                'type' => 'hankyung',
                'order' => 4
            ],
            [
                'website_name' => '헤럴드경제',
                'type' => 'heraldcorp',
                'order' => 5
            ],
            [
                'website_name' => '조세일보',
                'type' => 'joseilbo',
                'order' => 6
            ],
            [
                'website_name' => '매일경제',
                'type' => 'mk',
                'order' => 7
            ],
            [
                'website_name' => '머니투데이',
                'type' => 'mt',
                'order' => 8
            ],
            [
                'website_name' => '뉴스핌',
                'type' => 'newspim',
                'order' => 9
            ],
        ];

        foreach ($items as $item){
            \App\Models\NewsWebsite::firstOrCreate([
                'website_name' => $item['website_name'],
                'type' => $item['type'],
                'order' => $item['order'],
            ]);
        }
    }
}
