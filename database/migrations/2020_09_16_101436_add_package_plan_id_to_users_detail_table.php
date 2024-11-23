<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackagePlanIdToUsersDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('package_plan_id')->after('user_id')->nullable();
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
        Schema::table('users_detail', function (Blueprint $table) {
            $table->dropForeign('users_detail_package_plan_id_foreign');
            $table->dropColumn('package_plan_id');
        });
    }
}
