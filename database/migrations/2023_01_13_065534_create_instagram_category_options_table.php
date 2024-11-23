<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstagramCategoryOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instagram_category_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instagram_category_id')->default(0);
            $table->string('title');
            $table->float('price');
            $table->unsignedBigInteger('country_id')->default(0);
            $table->timestamps();

            $table->foreign('instagram_category_id')->references('id')->on('instagram_categories')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('instagram_category_options');
    }
}
