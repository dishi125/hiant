<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToUserCreditsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_credits_history', function (Blueprint $table) {
            $table->unsignedBigInteger('booked_user_id')->after('user_id')->nullable();
            $table->foreign('booked_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_credits_history', function (Blueprint $table) {
            $table->dropForeign('user_credits_history_booked_user_id_foreign');
            $table->dropColumn('booked_user_id');
        });
    }
}
