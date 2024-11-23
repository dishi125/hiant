<?php

use App\Models\UserCards;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCardStatusToUserCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_cards', function (Blueprint $table) {
            $table->string('card_level_status')->default(UserCards::NORMAL_STATUS);
        });
        Schema::table('user_card_levels', function (Blueprint $table) {
            $table->string('card_level_status')->default(UserCards::NORMAL_STATUS);
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
            $table->dropColumn('card_level_status');
        });
        Schema::table('user_card_levels', function (Blueprint $table) {
            $table->dropColumn('card_level_status');
        });
    }
}
