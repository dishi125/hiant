<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id')->index();            
            $table->string('main_name')->nullable();
            $table->string('shop_name')->nullable();
            $table->string('email')->nullable();
            $table->string('speciality_of')->nullable();
            $table->string('best_portfolio')->nullable();
            $table->string('business_licence')->nullable();
            $table->string('identification_card')->nullable();
            $table->string('recommended_code')->nullable();
            $table->string('mobile')->nullable();
            $table->unsignedBigInteger('manager_id')->default(0);
            $table->unsignedBigInteger('status_id')->index();            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('status')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
}
