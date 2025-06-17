<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotdealimagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotdealimages', function (Blueprint $table) {
            $table->id()->index();
            $table->foreignId('hotdeal_id')->unsigned();
            $table->foreign('hotdeal_id')->references('id')->on('hotdeals')->onDelete('cascade');
            $table->string('hotdeal_img_url');
            $table->integer('image_status')->default(1);
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
        Schema::dropIfExists('hotdealimages');
    }
}
