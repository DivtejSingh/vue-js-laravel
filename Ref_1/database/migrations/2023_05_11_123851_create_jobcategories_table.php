<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobcategories', function (Blueprint $table) {
            $table->id()->index();
            $table->string('job_cat_name')->unique();
            $table->string('job_img_url')->nullable();
            $table->string('job_cat_slug');
            $table->integer('job_cat_feature')->default(0);
            $table->integer('job_cat_sort')->default(0);
            $table->integer('job_cat_status')->default(1);
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
        Schema::dropIfExists('jobcategories');
    }
}
