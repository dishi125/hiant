<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityCommentReplyLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_comment_reply_likes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('community_comment_reply_id')->index();
            $table->unsignedBigInteger('user_id')->index();            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('community_comment_reply_id')->references('id')->on('community_comment_reply')->onDelete('cascade');
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
        Schema::dropIfExists('community_comment_reply_likes');
    }
}
