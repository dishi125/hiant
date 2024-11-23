<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBigCategoryLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('big_category_languages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('big_category_id')->index();
            $table->unsignedBigInteger('post_language_id')->index();
            $table->string('name')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('big_category_id')->references('id')->on('big_categories')->onDelete('cascade');
            $table->foreign('post_language_id')->references('id')->on('post_languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('big_category_languages');
    }
}
