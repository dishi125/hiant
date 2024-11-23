<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardLevelDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_level_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('main_card_id');
            $table->foreign('main_card_id')->references('id')->on('default_cards_rives')->onDelete('cascade');
            $table->string('card_name')->nullable();
            $table->text('background_rive')->nullable();
            $table->text('character_rive')->nullable();
            $table->text('download_file')->nullable();

            $table->string('usd_price')->default(0)->nullable();
                $table->string('japanese_yen_price')->default(0)->nullable();
            $table->string('chinese_yuan_price')->default(0)->nullable();
            $table->string('korean_won_price')->default(0)->nullable();

            $table->integer("required_love_in_days")->default(1);

            $table->unsignedBigInteger("card_level")->nullable();
            $table->foreign('card_level')->references('id')->on('card_levels')->onDelete('cascade');
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
        Schema::dropIfExists('card_level_details');
    }
}
