<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstagramCategoryLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instagram_category_languages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('entity_id')->comment('instagram_categories or instagram_category_options');
            $table->enum('entity_type', ['insta_category_name', 'insta_category_sub_title','insta_category_option']);
            $table->unsignedBigInteger('language_id')->index();
            $table->string('value')->nullable();
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
        Schema::dropIfExists('instagram_category_languages');
    }
}
