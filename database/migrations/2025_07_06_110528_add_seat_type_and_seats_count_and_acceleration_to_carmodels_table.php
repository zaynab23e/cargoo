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
        Schema::table('carmodels', function (Blueprint $table) {
            $table->enum('seat_type', [
                'electric',
                'accessible',
                'sport',
                'leather',
                'fabric',
            ])->nullable();
            $table->unsignedTinyInteger('seats_count')->nullable();
            $table->decimal('acceleration', 4, 2)->nullable();              
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carmodels', function (Blueprint $table) {
            $table->dropColumn('seat_type');
            $table->dropColumn('seats_count');
            $table->dropColumn('acceleration');
        });
    }
};
