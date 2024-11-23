<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBigCategorySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('big_category_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('big_category_id')->index();
            $table->foreign('big_category_id')->references('id')->on('big_categories')->onDelete('cascade');
            $table->boolean('is_show')->default(1);
            $table->integer('order')->default(0);
            $table->string('country_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('big_category_settings');
    }
}
