<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesNotificationStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages_notification_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_type_id')->index();
            $table->unsignedBigInteger('entity_id')->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('notification_status');
            $table->timestamps();

            $table->foreign('entity_type_id')->references('id')->on('entity_types')->onDelete('cascade');
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
        Schema::dropIfExists('messages_notification_status');
    }
}
