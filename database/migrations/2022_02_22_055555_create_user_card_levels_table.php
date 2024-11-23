<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCardLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_card_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_card_id');
            $table->foreign('user_card_id')->references('id')->on('user_cards')->onDelete('cascade');

            $table->text('background_riv')->nullable();
            $table->text('character_riv')->nullable();

            $table->unsignedBigInteger("card_level")->nullable();
            $table->foreign('card_level')->references('id')->on('card_levels')->onDelete('cascade');
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
        Schema::dropIfExists('user_card_levels');
    }
}
