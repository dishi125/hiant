<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCharacterThumbnailToDefaultCardsRives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('default_cards_rives', function (Blueprint $table) {
            $table->string('character_thumbnail')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('default_cards_rives', function (Blueprint $table) {
            $table->dropColumn('character_thumbnail');
        });
    }
}
