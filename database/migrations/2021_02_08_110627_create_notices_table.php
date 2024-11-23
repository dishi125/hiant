<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('notify_type');
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->unsignedBigInteger('entity_type_id')->nullable()->index(); 
            $table->unsignedBigInteger('entity_id')->nullable(); 
            $table->unsignedBigInteger('user_id')->nullable()->index(); 
            $table->unsignedBigInteger('to_user_id')->nullable()->index(); 
            $table->timestamps();

            $table->foreign('entity_type_id')->references('id')->on('entity_types')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notices');
    }
}
