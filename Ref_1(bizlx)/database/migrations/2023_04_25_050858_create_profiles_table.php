<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id()->index();
            $table->integer('user_id')->index();
            $table->string('user_avatar')->nullable();
            $table->longText('about')->nullable();
            $table->string('address');
            $table->integer('country_id')->default(1);
            $table->integer('state_id');
            $table->integer('city_id');
            $table->integer('profile_status')->default(1);
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
        Schema::dropIfExists('profiles');
    }
}
