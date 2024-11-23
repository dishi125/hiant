<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEntityTypeToInstagramCategoryLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instagram_category_languages', function (Blueprint $table) {
            $table->unsignedBigInteger('entity_id')->index()->comment('instagram_categories')->after('id');
            $table->enum('entity_type', ['insta_category_name', 'insta_category_sub_title'])->nullable()->after('entity_id');

            $table->foreign('entity_id')->references('id')->on('instagram_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instagram_category_languages', function (Blueprint $table) {
            $table->dropColumn('entity_id');
            $table->dropColumn('entity_type');
        });
    }
}
