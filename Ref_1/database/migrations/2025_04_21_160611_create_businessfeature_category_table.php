<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessfeatureCategoryTable extends Migration
{
    public function up()
    {
        Schema::create('businessfeature_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('businessfeature_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('businessfeature_category');
    }
}
