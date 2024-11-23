<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCardLevelToUserCardSellRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_card_sell_requests', function (Blueprint $table) {
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
        Schema::table('user_card_sell_requests', function (Blueprint $table) {
            $table->dropColumn('card_level');
        });
    }
}
