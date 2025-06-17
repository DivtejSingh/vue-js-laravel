<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('video_slug')->after('video_title')->unique()->nullable();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('sale_slug')->after('sale_title')->unique()->nullable();
        });

        Schema::table('hotdeals', function (Blueprint $table) {
            $table->string('hotdeal_slug')->after('hot_deal_title')->unique()->nullable();
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->string('job_slug')->after('job_title')->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videos');
        Schema::table('sales');
        Schema::table('hotdeals');
        Schema::table('jobs');

    }
}
