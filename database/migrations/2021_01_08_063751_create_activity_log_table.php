<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_type_id')->index();
            $table->unsignedBigInteger('entity_id')->index();  
            $table->unsignedBigInteger('request_booking_status_id')->nullable()->index(); 
            $table->unsignedBigInteger('user_id')->index(); 
            $table->boolean('is_cancelled_by_shop')->default(0);
            $table->string('country')->nullable();
            $table->timestamps();

            $table->foreign('entity_type_id')->references('id')->on('entity_types')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('request_booking_status_id')->references('id')->on('request_booking_status')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_log');
    }
}
