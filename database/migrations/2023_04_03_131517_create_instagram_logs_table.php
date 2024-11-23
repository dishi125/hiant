<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstagramLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instagram_logs', function (Blueprint $table) {
            $table->id();
            $table->string('social_id')->nullable();
            $table->string('instagram_id')->nullable();
            $table->integer('user_id');
            $table->integer('shop_id');
            $table->string('social_name')->nullable();
            $table->integer('status')->nullable();
            $table->boolean('is_admin_read')->default(1);
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
        Schema::dropIfExists('instagram_logs');
    }
}
