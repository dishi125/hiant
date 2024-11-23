<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopGlobalPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_global_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('shop_global_price_category_id')->index();
            $table->string('name')->nullable();
            $table->decimal('price', 15, 2)->default(0.00);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('shop_global_price_category_id')->references('id')->on('shop_global_price_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_global_prices');
    }
}
