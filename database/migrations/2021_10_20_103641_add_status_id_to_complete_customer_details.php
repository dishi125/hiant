<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusIdToCompleteCustomerDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('complete_customer_details', function (Blueprint $table) {
            $table->unsignedBigInteger('entity_type_id')->index()->nullable();
            $table->foreign('entity_type_id')->references('id')->on('entity_types')->onDelete('cascade');
            $table->unsignedBigInteger('entity_id')->index()->nullable();
            $table->unsignedBigInteger('status_id')->index()->nullable();
            $table->foreign('status_id')->references('id')->on('request_booking_status')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('complete_customer_details', function (Blueprint $table) {
            $table->dropForeign('complete_customer_details_entity_type_id_foreign');
            $table->dropForeign('complete_customer_details_status_id_foreign'); 
            $table->dropColumn('entity_type_id');
            $table->dropColumn('entity_id');
            $table->dropColumn('status_id');
        });
    }
}
