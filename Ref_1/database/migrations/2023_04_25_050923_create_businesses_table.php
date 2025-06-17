<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id()->index();
            $table->integer('business_id')->index();
            $table->integer('cat_id')->index();
            $table->string('sub_cat_id')->nullable();
            $table->string('gst')->nullable();
            $table->longText('business_info')->nullable();
            $table->integer('plan_id')->nullable();
            $table->integer('listedby')->default(0);
            $table->integer('listedby_reseller_id')->nullable();
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
        Schema::dropIfExists('businesses');
    }
}
