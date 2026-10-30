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
    Schema::table('show_bookings', function (Blueprint $table) {
        $table->string('ticket_type')->default('populaire')->after('seat_number');
        $table->decimal('amount_paid', 10, 2)->default(0.00)->after('ticket_type');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('show_bookings', function (Blueprint $table) {
            //
        });
    }
};
