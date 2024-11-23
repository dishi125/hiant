<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopPriceImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_price_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_price_id');
            $table->string('image');
            $table->timestamps();
            $table->foreign('shop_price_id')->references('id')->on('shop_prices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_price_images');
    }
}
