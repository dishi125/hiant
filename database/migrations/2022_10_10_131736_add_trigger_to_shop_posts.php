<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTriggerToShopPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //AND `shop_posts`.`deleted_at` IS NULL 
        DB::unprepared("CREATE TRIGGER `add_trigger_for_duplicate` BEFORE INSERT ON `shop_posts` FOR EACH ROW
                BEGIN

                SELECT created_at INTO @created_at FROM `shop_posts` WHERE 
                    `shop_posts`.`shop_id` = (NEW.shop_id) AND 
                    `shop_posts`.`type` = (NEW.type) AND 
                    `shop_posts`.`instagram_post_id` = (NEW.instagram_post_id)                     
                    ORDER BY `shop_posts`.`id` DESC;
                            
                IF @created_at != '' THEN
                    SIGNAL SQLSTATE '02000' SET MESSAGE_TEXT = 'Sorry, this post already instered.';
                END IF;

                END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP TRIGGER `add_trigger_for_duplicate`");
    }
}
