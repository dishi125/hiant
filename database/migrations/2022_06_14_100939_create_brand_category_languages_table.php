<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandCategoryLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_category_languages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_category_id')->index();
            $table->foreign('brand_category_id')->references('id')->on('brand_categories')->onDelete('cascade');

            $table->unsignedBigInteger('post_language_id')->index();  
            $table->foreign('post_language_id')->references('id')->on('post_languages')->onDelete('cascade');
            
            $table->string('name')->nullable();
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
        Schema::dropIfExists('brand_category_languages');
    }
}
