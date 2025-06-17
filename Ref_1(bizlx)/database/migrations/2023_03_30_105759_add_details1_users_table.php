<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetails1UsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->bigInteger('mobile_phone')->unique()->after('email');
            $table->string('username')->unique()->after('email');
            $table->enum('user_status',['0','1'])->default('0')->after('email');
            $table->enum('user_role',['0','1','2','3','4'])->default('0')->after('email');
            $table->string('locale')->default('en')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
