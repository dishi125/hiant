<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetalkOptionLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metalk_option_languages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('metalk_options_id')->index();
            $table->unsignedBigInteger('language_id')->index();  
            $table->string('value')->nullable();
            $table->timestamps();

            $table->foreign('metalk_options_id')->references('id')->on('metalk_options');
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
        Schema::dropIfExists('metalk_option_languages');
    }
}
