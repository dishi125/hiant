<?php

use App\Models\UserCards;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCardResetHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_card_reset_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('sell_card_id')->nullable();
            $table->foreign('sell_card_id')->references('id')->on('user_card_sell_requests')->onDelete('cascade');

            $table->unsignedBigInteger("card_level")->default(1);

            $table->unsignedBigInteger("love_count")->default(0);

            $table->string('card_level_status')->default(UserCards::NORMAL_STATUS);

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
        Schema::dropIfExists('user_card_reset_histories');
    }
}
