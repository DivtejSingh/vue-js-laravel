<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotdealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotdeals', function (Blueprint $table) {
            $table->id()->index();
            $table->foreignId('business_id')->unsigned()->index();
            $table->foreign('business_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->string('hot_deal_title');
            $table->longText('hot_deal_description');
            $table->bigInteger('price_from');
            $table->bigInteger('price_to');
            $table->date('date_from');
            $table->date('date_to');
            $table->integer('hot_deal_status')->default(1);
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
        Schema::dropIfExists('hotdeals');
    }
}
