<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCancelledByShopToRequestedCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requested_customer', function (Blueprint $table) {
            $table->boolean('is_cancelled_by_shop')->after('request_booking_status_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requested_customer', function (Blueprint $table) {
            $table->dropColumn('is_cancelled_by_shop');
        });
    }
}
