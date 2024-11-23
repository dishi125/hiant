<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsOnUserDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_detail', function (Blueprint $table) {
            $table->timestamp('points_updated_on')->useCurrent();
            $table->unsignedBigInteger('points')->default(0);
            $table->unsignedBigInteger('level')->default(1);
            $table->unsignedBigInteger('count_days')->default(1);
            $table->unsignedBigInteger('card_number')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_detail', function (Blueprint $table) {
            $table->dropColumn('points_updated_on');
            $table->dropColumn('points');
            $table->dropColumn('level');
            $table->dropColumn('count_days');
            $table->dropColumn('card_number');
        });
    }
}
