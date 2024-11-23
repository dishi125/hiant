<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUserCreditsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_credits_history', function (Blueprint $table) {
            $table->enum('type', ['Regular', 'Default','Reload','Chating','Penalty','Reward','Recommended'])->default('Regular')->after('transaction');
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
            $table->dropColumn('type');
        });
    }
}
