<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToReportClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_clients', function (Blueprint $table) {
            $table->unsignedBigInteger('reported_user_id')->after('user_id')->nullable();
            $table->foreign('reported_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_clients', function (Blueprint $table) {
            $table->dropForeign('report_clients_reported_user_id_foreign');
            $table->dropColumn('reported_user_id');
        });
    }
}
