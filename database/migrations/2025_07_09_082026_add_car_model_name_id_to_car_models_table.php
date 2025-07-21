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
            $table->unsignedBigInteger('model_name_id')->after('acceleration');
            $table->foreign('model_name_id')->references('id')->on('model_names')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carmodels', function (Blueprint $table) {
            $table->dropForeign(['model_name_id']);
            $table->dropColumn('model_name_id');
        });
    }
};
