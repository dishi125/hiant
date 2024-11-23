<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupChatRoomMessageFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_chat_room_message_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('group_id');
            $table->string('file');
            $table->string('video_thumbnail')->nullable();
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('group_chat_room_messages')->onDelete('cascade');
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
        Schema::dropIfExists('group_chat_room_message_files');
    }
}
