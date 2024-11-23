<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopReportAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_report_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_report_id')->index();  
            $table->foreign('shop_report_id')->references('id')->on('shop_report_histories')->onDelete('cascade');
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
        Schema::dropIfExists('shop_report_attachments');
    }
}
