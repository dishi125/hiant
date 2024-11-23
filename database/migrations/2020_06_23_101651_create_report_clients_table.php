<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('report_type_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('entity_id')->index();
            $table->unsignedBigInteger('category_id')->index();
            $table->integer('status_count')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('report_type_id')->references('id')->on('report_types')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_clients');
    }
}
