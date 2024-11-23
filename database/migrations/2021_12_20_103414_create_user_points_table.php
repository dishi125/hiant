<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_points', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('entity_type')->nullable()->comment = "like_my_community_post, like_my_review_post, upload_community_post, review_shop_post, review_hospital_post,           comment_on_my_community_post,like_my_shop_post";
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('entity_created_by_id')->nullable();
            $table->unsignedBigInteger('points');

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
        Schema::dropIfExists('user_points');
    }
}
