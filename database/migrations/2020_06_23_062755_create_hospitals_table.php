<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospitals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id')->index()->nullable(); 
            $table->string('main_name')->nullable();
            $table->string('email')->nullable();
            $table->string('interior_photo')->nullable();
            $table->string('business_licence')->nullable();
            $table->string('recommended_code')->nullable();
            $table->string('mobile')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('manager_id')->default(0);
            $table->unsignedBigInteger('status_id')->index();            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
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
        Schema::dropIfExists('hospitals');
    }
}
