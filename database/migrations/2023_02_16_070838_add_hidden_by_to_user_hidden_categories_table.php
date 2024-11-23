<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHiddenByToUserHiddenCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_hidden_categories', function (Blueprint $table) {
            $table->enum('hidden_by',['admin','user'])->default('user')->after('user_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_hidden_categories', function (Blueprint $table) {
            $table->dropColumn('hidden_by');
        });
    }
}
