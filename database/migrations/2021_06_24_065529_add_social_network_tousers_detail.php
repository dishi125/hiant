<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialNetworkTousersDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_detail', function (Blueprint $table) {
            $table->string('sns_type')->nullable()->after('country_id');
            $table->string('sns_link')->nullable()->after('country_id');
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
            $table->dropColumn('sns_type');
            $table->dropColumn('sns_link');
        });
    }
}
