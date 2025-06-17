<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id()->index();
            $table->foreignId('review_user_id')->unsigned()->index();
            $table->foreign('review_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('review_business_id')->unsigned()->index();
            $table->foreign('review_business_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('rating')->default(5);
            $table->string('review_text')->default('Good');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
