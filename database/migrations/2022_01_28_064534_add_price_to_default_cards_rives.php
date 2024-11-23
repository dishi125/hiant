<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToDefaultCardsRives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('default_cards_rives', function (Blueprint $table) {
            $table->string('usd_price')->default(0)->nullable();
            $table->string('japanese_yen_price')->default(0)->nullable();
            $table->string('chinese_yuan_price')->default(0)->nullable();
            $table->string('korean_won_price')->default(0)->nullable();
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
            $table->dropColumn('usd_price');
            $table->dropColumn('japanese_yen_price');
            $table->dropColumn('chinese_yuan_price');
            $table->dropColumn('korean_won_price');
        });
    }
}
