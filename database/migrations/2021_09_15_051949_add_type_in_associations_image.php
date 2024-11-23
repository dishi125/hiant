<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeInAssociationsImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('associations_image', function (Blueprint $table) {
            $table->string('type')->default('main_image')->after('associations_id')->comment="main_image / banner_image";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('associations_image', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
