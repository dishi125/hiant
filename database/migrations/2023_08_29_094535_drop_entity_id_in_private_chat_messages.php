<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropEntityIdInPrivateChatMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_chat_messages', function (Blueprint $table) {
//            $table->dropForeign('private_chat_messages_entity_type_id_foreign');
            $table->dropColumn('entity_type_id');

//            $table->dropForeign('private_chat_messages_entity_id_foreign');
            $table->dropColumn('entity_id');

            $table->text('message')->nullable()->change();
            $table->dropColumn('is_post_image');
            $table->dropColumn('is_default_message');
            $table->dropColumn('is_guest');
            $table->dropColumn('is_admin_message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_chat_messages', function (Blueprint $table) {
            //
        });
    }
}
