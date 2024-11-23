<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('entity_type_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('category_id')->index()->nullable();
            $table->string('name');            
            $table->text('address')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('email')->nullable();
            $table->string('recommended_code')->nullable();
            $table->string('business_licence')->nullable();
            $table->string('best_portfolio')->nullable();
            $table->string('identification_card')->nullable();
            $table->string('interior_photo')->nullable();
            $table->unsignedBigInteger('manager_id')->default(0);
            $table->unsignedBigInteger('request_status_id')->index();
            $table->integer('request_count')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('entity_type_id')->references('id')->on('entity_types')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
            // $table->foreign('manager_id')->references('id')->on('managers')->onDelete('cascade');
            $table->foreign('request_status_id')->references('id')->on('request_form_status')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_forms');
    }
}
