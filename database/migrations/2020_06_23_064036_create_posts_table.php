<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('hospital_id')->index();
            $table->unsignedBigInteger('category_id')->index();
            $table->string('title')->nullable();            
            $table->string('sub_title')->nullable(); 
            $table->dateTime('from_date', 0);           
            $table->dateTime('to_date', 0);    
            $table->decimal('before_price', 15, 2)->default(0.00);
            $table->decimal('final_price', 15, 2)->default(0.00);        
            $table->float('discount_percentage', 8, 2)->nullable();
            $table->boolean('is_discount')->default(1);
            $table->integer('views_count')->default(0);
            $table->unsignedBigInteger('status_id')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('status')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
