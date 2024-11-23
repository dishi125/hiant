<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportedUserAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reported_user_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_report_id')->index();
            $table->foreign('user_report_id')->references('id')->on('reported_users')->onDelete('cascade');
            $table->string('attachment_item')->nullable();
            $table->enum('type', ['image', 'video'])->default('image');
            $table->string('video_thumbnail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reported_user_attachments');
    }
}
