<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupChatRoomJoinedUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_chat_room_joined_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_chat_room_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('group_chat_room_id')->references('id')->on('group_chat_rooms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_chat_room_joined_users');
    }
}
