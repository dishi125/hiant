<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('shop_price_category_id')->index();
            $table->string('name')->nullable();   
            $table->decimal('price', 15, 2)->default(0.00);
            $table->decimal('discount', 15, 2)->default(0.00);         
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('shop_price_category_id')->references('id')->on('shop_price_category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_prices');
    }
}
