<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefaultCardsRivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_cards_rives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('default_card_id'); 
            $table->foreign('default_card_id')->references('id')->on('default_cards')->onDelete('cascade');

            $table->string('card_name')->nullable();
            $table->text('background_rive');
            $table->text('character_rive');
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
        Schema::dropIfExists('default_cards_rives');
    }
}
