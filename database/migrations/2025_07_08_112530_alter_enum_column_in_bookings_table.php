<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
    {
        // Step 1: Temporarily convert to TEXT to avoid ENUM conflict
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status TEXT");

        // Step 2: Optional - remap old values to match new enum values (if needed)
        DB::statement("UPDATE bookings SET status = 'initiated'");

        // Step 3: Recreate ENUM column with new values
        DB::statement("
            ALTER TABLE bookings 
            MODIFY COLUMN status 
            ENUM('initiated', 'awaiting_payment', 'payment_pending', 'confirmed', 'assigned', 'canceled', 'completed') 
            DEFAULT 'initiated' 
            NOT NULL
        ");
    }

    public function down(): void
    {
        // Reverse: convert to TEXT first
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status TEXT");

        // Optional: restore old values
        DB::statement("UPDATE bookings SET status = 'pending'");

        // Restore old ENUM
        DB::statement("
            ALTER TABLE bookings 
            MODIFY COLUMN status 
            ENUM('pending', 'confirmed', 'assigned', 'canceled', 'completed') 
            DEFAULT 'pending' 
            NOT NULL
        ");
    }
};
