<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssociationCommunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('association_communities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('associations_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('title');
            $table->string('description');
            $table->integer('views_count')->default(0);
            $table->string('country_code')->nullable();
            $table->integer('is_pin')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('associations_id')->references('id')->on('associations');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('association_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('association_communities');
    }
}
