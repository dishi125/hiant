<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThumbUrlToShopPriceImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_price_images', function (Blueprint $table) {
            $table->string('thumb_url');
            $table->double('order', 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_price_images', function (Blueprint $table) {
            $table->dropColumn('thumb_url');
            $table->dropColumn('order');
        });
    }
}
