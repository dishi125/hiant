<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('entity_type_id')->index();
            $table->unsignedBigInteger('package_plan_id')->index();
            $table->integer('deduct_rate')->nullable();
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->float('km', 8, 2)->nullable();
            $table->integer('no_of_posts')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('entity_type_id')->references('id')->on('entity_types')->onDelete('cascade');
            $table->foreign('package_plan_id')->references('id')->on('package_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_plans');
    }
}
