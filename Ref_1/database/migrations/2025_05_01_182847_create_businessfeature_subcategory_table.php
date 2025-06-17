<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessfeatureSubcategoryTable extends Migration
{
    public function up()
    {
        Schema::create('businessfeature_subcategory', function (Blueprint $table) {
            $table->id();

            // link back to your businessfeatures table
            $table->foreignId('businessfeature_id')
                  ->constrained('businessfeatures')
                  ->onDelete('cascade');

            // link forward to your subcategories table
            $table->foreignId('subcategory_id')
                  ->constrained('subcategories')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('businessfeature_subcategory');
    }
}
