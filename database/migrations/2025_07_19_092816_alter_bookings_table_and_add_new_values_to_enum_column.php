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
    // Step 1: Temporarily change column type to TEXT to bypass ENUM limitations
    DB::statement("ALTER TABLE bookings MODIFY COLUMN status TEXT");

    // Step 2: Update old status value from 'assigned' to 'car_assigned'
    DB::statement("UPDATE bookings SET status = 'driver_assigned' WHERE status = 'assigned'");

    // Step 3: Alter column back to ENUM with new statuses
    DB::statement("
        ALTER TABLE bookings 
        MODIFY COLUMN status 
        ENUM(
            'initiated', 
            'awaiting_payment', 
            'payment_pending', 
            'confirmed', 
            'car_assigned', 
            'driver_assigned', 
            'driver_accepted', 
            'canceled', 
            'completed'
        ) 
        DEFAULT 'initiated' 
        NOT NULL
    ");
}

public function down(): void
{
    // Step 1: Convert ENUM back to TEXT to allow reversion
    DB::statement("ALTER TABLE bookings MODIFY COLUMN status TEXT");

    // Step 2: Revert 'car_assigned' back to 'assigned'
    DB::statement("UPDATE bookings SET status = 'assigned' WHERE status = 'driver_assigned'");

    // Step 3: Drop new statuses and restore old ENUM
    DB::statement("
        ALTER TABLE bookings 
        MODIFY COLUMN status 
        ENUM(
            'initiated', 
            'awaiting_payment', 
            'payment_pending', 
            'confirmed', 
            'assigned', 
            'canceled', 
            'completed'
        ) 
        DEFAULT 'initiated' 
        NOT NULL
    ");
}

};
