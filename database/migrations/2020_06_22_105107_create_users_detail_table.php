<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('gender')->nullable();
            $table->string('avatar')->nullable();
            $table->unsignedBigInteger('device_type_id')->unsigned()->nullable();
            $table->string("device_id")->nullable();
            $table->string("device_token")->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->unsignedBigInteger('country_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('manager_id')->references('id')->on('managers');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('device_type_id')->references('id')->on('device_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_detail');
    }
}
