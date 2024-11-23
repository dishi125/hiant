<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMenuColumnToCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category', function (Blueprint $table) {
            $table->string('menu_key')->default('home');
        });
        Schema::table('category_settings', function (Blueprint $table) {
            $table->string('menu_key')->default('home');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category', function (Blueprint $table) {
            $table->dropColumn('menu_key');
        });
        Schema::table('category_settings', function (Blueprint $table) {
            $table->dropColumn('menu_key');
        });
    }
}
