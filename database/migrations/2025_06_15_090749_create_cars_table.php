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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carmodel_id');
            $table->string('plate_number')->unique();
            $table->enum('status', ["available", "rented", "maintenance"])->default('available');
            $table->string('image');
            $table->string('color');
            $table->foreign('carmodel_id')->references('id')->on('carmodels')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
