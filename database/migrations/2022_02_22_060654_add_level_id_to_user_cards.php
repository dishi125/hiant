<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLevelIdToUserCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_cards', function (Blueprint $table) {
            $table->unsignedBigInteger("card_level")->default(1);
            $table->unsignedBigInteger("active_level")->default(1);
            $table->unsignedBigInteger("love_count")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_cards', function (Blueprint $table) {
            $table->dropColumn('card_level');
            $table->dropColumn('active_level');
            $table->dropColumn('love_count');
        });
    }
}
