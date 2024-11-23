<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportGroupMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_group_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporter_user_id');
            $table->unsignedBigInteger('message_id');
            $table->boolean('is_admin_read')->default(1);
            $table->timestamps();

            $table->foreign('reporter_user_id')->references('id')->on('users');
            $table->foreign('message_id')->references('id')->on('group_messages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_group_messages');
    }
}
