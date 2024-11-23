<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSavedHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_saved_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('saved_history_type_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('entity_id')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('saved_history_type_id')->references('id')->on('saved_history_types')->onDelete('cascade');
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
        Schema::dropIfExists('user_saved_history');
    }
}
