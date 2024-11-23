<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInUserBlockHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_block_history', function (Blueprint $table) {
            $table->string('block_for')->nullable()->after('is_block')->comment = 'chat / video_call / community_post / audio_call';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_block_history', function (Blueprint $table) {
            //
        });
    }
}
