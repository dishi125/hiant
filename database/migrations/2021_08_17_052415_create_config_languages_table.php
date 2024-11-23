<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_languages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('config_id')->index();
            $table->unsignedBigInteger('language_id')->index();  
            $table->string('value')->nullable();
            $table->timestamps();

            $table->foreign('config_id')->references('id')->on('config')->onDelete('cascade');
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
        Schema::dropIfExists('config_languages');
    }
}
