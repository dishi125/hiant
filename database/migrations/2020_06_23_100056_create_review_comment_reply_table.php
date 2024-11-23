<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewCommentReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('review_comment_reply', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('review_comment_id')->index();
            $table->unsignedBigInteger('user_id')->index();            
            $table->text('comment');            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('review_comment_id')->references('id')->on('review_comments')->onDelete('cascade');
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
        Schema::dropIfExists('review_comment_reply');
    }
}
