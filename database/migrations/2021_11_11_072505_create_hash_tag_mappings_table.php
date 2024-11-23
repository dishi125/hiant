<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHashTagMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hash_tag_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hash_tag_id')->index();
            $table->foreign('hash_tag_id')->references('id')->on('hash_tags')->onDelete('cascade');

            $table->unsignedBigInteger('entity_id')->index();
            $table->integer('entity_type_id')->nullable();
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
        Schema::dropIfExists('hash_tag_mappings');
    }
}
