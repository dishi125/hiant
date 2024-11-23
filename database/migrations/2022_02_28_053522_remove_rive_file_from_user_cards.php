<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRiveFileFromUserCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_cards', function (Blueprint $table) {
            $table->dropColumn('background_riv');
            $table->dropColumn('character_riv');
        });
        Schema::table('user_card_levels', function (Blueprint $table) {
            $table->dropColumn('background_riv');
            $table->dropColumn('character_riv');
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
            $table->text('background_riv')->nullable();
            $table->text('character_riv')->nullable();
        });
        Schema::table('user_card_levels', function (Blueprint $table) {
            $table->text('background_riv')->nullable();
            $table->text('character_riv')->nullable();
        });
    }
}
