<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referred_by')->index();  
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('referral_user')->index();  
            $table->foreign('referral_user')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('has_coffee_access')->default(0);
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
        Schema::dropIfExists('user_referrals');
    }
}
