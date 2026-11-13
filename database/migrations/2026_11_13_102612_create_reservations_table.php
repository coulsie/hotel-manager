<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();

            // Dates prévues du séjour
            $table->date('check_in_date');
            $table->date('check_out_date');

            // Détails financiers
            $table->integer('nights')->default(1);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('deposit_paid', 10, 2)->default(0); // Acompte versé

            // Statut et Fiche de police
            $table->enum('status', ['confirmed', 'checked_in', 'cancelled', 'no_show'])->default('confirmed');
            $table->string('provenance')->nullable();
            $table->string('destination')->nullable();
            $table->text('special_requests')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
