<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopPostLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_post_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_post_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('shop_post_id')->references('id')->on('shop_posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('shop_post_likes');
    }
}
