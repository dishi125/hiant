<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAdminReadToReloadCoinsRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reload_coins_request', function (Blueprint $table) {
            $table->boolean('is_admin_read')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reload_coins_request', function (Blueprint $table) {
            $table->dropColumn('is_admin_read');
        });
    }
}
