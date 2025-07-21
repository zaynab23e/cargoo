<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('car_model_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_model_id');
            $table->string('image');
            $table->foreign('car_model_id')->references('id')->on('carmodels')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_model_images');
    }
};
