<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_type_id')->index();
            $table->enum('type', ['default', 'custom'])->default('default');
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->string('name');
            $table->string('logo')->nullable();
            $table->unsignedBigInteger('status_id')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_type_id')->references('id')->on('category_types')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('status')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category');
    }
}
