<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstagramCategoryOptionLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instagram_category_option_languages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('entity_id')->index()->comment('instagram_category_options');
            $table->unsignedBigInteger('language_id')->index();
            $table->string('title')->nullable();
            $table->float('price')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();

            $table->foreign('entity_id')->references('id')->on('instagram_category_options')->onDelete('cascade');
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
        Schema::dropIfExists('instagram_category_option_languages');
    }
}
