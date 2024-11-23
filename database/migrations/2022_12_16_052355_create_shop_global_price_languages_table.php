<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopGlobalPriceLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_global_price_languages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('entity_id')->comment('shop_global_price_categories or shop_global_prices');
            $table->enum('entity_type', ['category', 'price']);
            $table->unsignedBigInteger('language_id')->index();
            $table->string('name')->nullable();
            $table->timestamps();

            $table->foreign('language_id')->references('id')->on('post_languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_global_price_languages');
    }
}
