<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetalkDropdownsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metalk_dropdowns', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('label');
            $table->unsignedBigInteger('metalk_options_id')->nullable();
            $table->foreign('metalk_options_id')->references('id')->on('metalk_options');
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
        Schema::dropIfExists('metalk_dropdowns');
    }
}
