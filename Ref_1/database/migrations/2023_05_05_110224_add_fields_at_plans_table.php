<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsAtPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {

            $table->integer('sale')->after('plan_status'); 
            $table->integer('video')->after('sale');
            $table->integer('about')->after('video');
            $table->integer('images')->after('about');
            $table->integer('contact')->after('images');
            $table->integer('reviews')->after('contact');
            $table->integer('services')->after('reviews');
            $table->integer('hot_deals')->after('services');
            $table->integer('featured_city')->after('hot_deals');
            $table->integer('home_page_banner')->after('featured_city');
            $table->integer('featured_category')->after('home_page_banner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            //
        });
    }
}
