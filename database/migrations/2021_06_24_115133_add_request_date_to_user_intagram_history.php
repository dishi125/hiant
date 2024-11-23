<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestDateToUserIntagramHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_intagram_history', function (Blueprint $table) {
            $table->dateTime('requested_at');
            $table->integer('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_intagram_history', function (Blueprint $table) {
            $table->dropColumn('requested_at');
            $table->dropColumn('status');
        });
    }
}
