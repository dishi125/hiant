<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('entity_type_id');
            $table->unsignedBigInteger('entity_id');
            $table->string('address');
            $table->string('address2')->nullable();
            $table->string('zipcode')->nullable();
            $table->float('latitude', 15, 4)->default(0.0)->nullable();
            $table->float('longitude', 15, 4)->default(0.0)->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->boolean('main_address')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('entity_type_id')->references('id')->on('entity_types');
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
        Schema::dropIfExists('addresses');
    }
}
