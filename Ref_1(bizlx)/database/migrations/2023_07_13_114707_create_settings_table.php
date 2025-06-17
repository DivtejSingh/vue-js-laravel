<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->integer('locale_id')->index()->nullable();
            $table->integer('timezone_id')->index()->nullable();
            $table->integer('currency_id')->index()->nullable();
            $table->string('business_reg_img')->nullable();
            $table->string('reseller_reg_img')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();
            $table->enum('business_status',['0','1'])->default('1');
            $table->enum('reseller_status',['0','1  '])->default('1');
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
        Schema::dropIfExists('settings');
    }
}
