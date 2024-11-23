<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToReviewCommentReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('review_comment_reply', function (Blueprint $table) {
            $table->unsignedBigInteger('reply_parent_id')->after('review_comment_id')->nullable();
            $table->foreign('reply_parent_id')->references('id')->on('review_comment_reply')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('review_comment_reply', function (Blueprint $table) {
            $table->dropForeign('review_comment_reply_reply_parent_id_foreign');
            $table->dropColumn('reply_parent_id');
        });
    }
}
