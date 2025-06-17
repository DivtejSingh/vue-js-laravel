<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessfeatureCityTable extends Migration
{
    public function up()
    {
        Schema::create('businessfeature_city', function (Blueprint $table) {
            $table->id();
            $table->foreignId('businessfeature_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('businessfeature_city');
    }
}

