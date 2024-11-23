<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToNewsPostViewHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('news_post_view_histories', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->string('website_name')->nullable();
            $table->text('headline')->nullable();
            $table->text('description')->nullable();
            $table->string('time')->nullable();
            $table->text('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news_post_view_histories', function (Blueprint $table) {
            //
        });
    }
}
