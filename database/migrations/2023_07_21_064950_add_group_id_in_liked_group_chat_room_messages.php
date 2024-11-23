<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupIdInLikedGroupChatRoomMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('liked_group_chat_room_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->nullable();

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
        Schema::table('liked_group_chat_room_messages', function (Blueprint $table) {
            //
        });
    }
}
