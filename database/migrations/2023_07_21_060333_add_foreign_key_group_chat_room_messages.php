<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyGroupChatRoomMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_chat_room_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->change();

            $table->foreign('group_id')->references('id')->on('group_chat_rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_chat_room_messages', function (Blueprint $table) {
            //
        });
    }
}
