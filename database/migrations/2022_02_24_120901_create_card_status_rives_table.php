<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardStatusRivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_status_rives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->foreign('card_id')->references('id')->on('default_cards_rives')->onDelete('cascade');

            $table->unsignedBigInteger('card_level_id');
            $table->foreign('card_level_id')->references('id')->on('card_levels')->onDelete('cascade');

            $table->string('card_level_status');
            $table->text('character_riv')->nullable();
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
        Schema::dropIfExists('card_status_rives');
    }
}
