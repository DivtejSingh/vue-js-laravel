<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeresellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('changeresellers', function (Blueprint $table) {
            $table->id()->index();
            $table->integer('business_id');
            $table->integer('old_reseller_id');
            $table->integer('new_reseller_id');
            $table->longText('reason_text');
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
        Schema::dropIfExists('changeresellers');
    }
}
