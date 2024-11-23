<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEntityTypeIdToShopDetailLanguages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_detail_languages', function (Blueprint $table) {
            $table->dropForeign('shop_detail_languages_shop_id_foreign');
            $table->unsignedBigInteger('entity_type_id')->index()->nullable();
            $table->foreign('entity_type_id')->references('id')->on('entity_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_detail_languages', function (Blueprint $table) {
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->dropForeign('shop_detail_languages_entity_type_id_foreign');
            $table->dropColumn('entity_type_id');
        });
    }
}
