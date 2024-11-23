<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGifticonAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gifticon_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gifticon_id')->index();
            $table->foreign('gifticon_id')->references('id')->on('gifticon_details')->onDelete('cascade');
            $table->string('attachment_item')->nullable();
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
        Schema::dropIfExists('gifticon_attachments');
    }
}
