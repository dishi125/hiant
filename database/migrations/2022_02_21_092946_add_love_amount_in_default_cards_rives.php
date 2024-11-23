<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoveAmountInDefaultCardsRives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('default_cards_rives', function (Blueprint $table) {
            $table->integer("required_love_in_days")->default(1);
            $table->unsignedBigInteger("card_level")->default(1);
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
            $table->dropColumn("required_love_in_days");
            $table->dropColumn("card_level");
        });
    }
}
