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
        Schema::create('show_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->onDelete('cascade');
            // Lié à l'utilisateur qui réserve
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Optionnel : Permet de facturer le billet directement sur la chambre
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->string('seat_number', 10)->nullable();
            $table->string('status')->default('confirmed'); // 'confirmed', 'canceled', 'charged_to_room'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('show_bookings');
    }
};
