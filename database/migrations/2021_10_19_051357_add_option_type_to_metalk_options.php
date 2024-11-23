<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionTypeToMetalkOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metalk_options', function (Blueprint $table) {
            $table->integer('options_type')->default(1);
            $table->boolean('is_different_lang')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metalk_options', function (Blueprint $table) {
            $table->dropColumn('options_type');
            $table->dropColumn('is_different_lang');
        });
    }
}
