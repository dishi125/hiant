<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssociationCommunityImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('association_community_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('community_id')->index();
            $table->string('image');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('community_id')->references('id')->on('association_communities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('association_community_images');
    }
}
