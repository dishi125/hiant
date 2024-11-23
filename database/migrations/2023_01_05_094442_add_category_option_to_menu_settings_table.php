<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryOptionToMenuSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_settings', function (Blueprint $table) {
            $table->tinyInteger('category_option')->default(0)->comment('0->small category, 1->big category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_settings', function (Blueprint $table) {
            $table->dropColumn('category_option');
        });
    }
}
