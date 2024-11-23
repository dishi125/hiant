<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNonLoginNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_login_notices', function (Blueprint $table) {
            $table->id();
            $table->string('notify_type');
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->unsignedBigInteger('entity_type_id')->nullable()->index(); 
            $table->unsignedBigInteger('entity_id')->nullable(); 
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('non_login_user_details')->onDelete('cascade');
            $table->boolean('is_read')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('non_login_notices');
    }
}
