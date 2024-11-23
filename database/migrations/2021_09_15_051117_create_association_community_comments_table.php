<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssociationCommunityCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('association_community_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('community_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->text('comment');
            $table->integer('parent_id')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('community_id')->references('id')->on('association_communities')->onDelete('cascade');
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
        Schema::dropIfExists('association_community_comments');
    }
}
