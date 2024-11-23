<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultipleShopPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multiple_shop_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_posts_id')->index();
            $table->string('post_item')->nullable();   
            $table->enum('type', ['image', 'video'])->default('image');      
            $table->string('video_thumbnail')->nullable();      
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('shop_posts_id')->references('id')->on('shop_posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('multiple_shop_posts');
    }
}
