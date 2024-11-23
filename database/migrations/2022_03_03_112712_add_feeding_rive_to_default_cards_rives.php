<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeedingRiveToDefaultCardsRives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('default_cards_rives', function (Blueprint $table) {
            $table->text('feeding_rive')->nullable();
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
            $table->dropColumn('feeding_rive');
        });
    }
}
