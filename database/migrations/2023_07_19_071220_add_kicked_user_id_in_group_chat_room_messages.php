<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKickedUserIdInGroupChatRoomMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_chat_room_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('kicked_user_id')->nullable();
            $table->longText('message')->nullable()->change();
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
