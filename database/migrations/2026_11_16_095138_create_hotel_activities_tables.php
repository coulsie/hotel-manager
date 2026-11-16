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
    // 1. Catalogue des activités et prestations de l'hôtel
    Schema::create('hotel_activities', function (Blueprint $table) {
        $table->id();
        $table->string('category'); // 'piscine', 'spa', 'seminaire', 'coworking', 'navette', 'boutique'
        $table->string('name');     // Ex: "Pass Piscine Adulte + Transat", "Navette Aéroport FHB", "Salle Réunion 1/2 Journée"
        $table->decimal('price', 10, 2);
        $table->string('unit')->default('forfait'); // 'forfait', 'heure', 'jour', 'trajet', 'personne'
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    // 2. Ventes & Réservations d'activités (Liées à une chambre OU client externe)
    Schema::create('activity_bookings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('hotel_activity_id')->constrained('hotel_activities')->onDelete('cascade');
        $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete(); // Si imputé sur chambre
        $table->string('client_name')->nullable(); // Si client de passage / externe
        $table->integer('quantity')->default(1);
        $table->decimal('unit_price', 10, 2);
        $table->decimal('total_amount', 10, 2);
        $table->string('payment_status')->default('unpaid'); // 'paid' ou 'unpaid' (en encours chambre)
        $table->text('notes')->nullable(); // Ex: Heure vol, créneau massage, n° vol...
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
      public function down(): void
    {
        Schema::dropIfExists('activity_bookings');
        Schema::dropIfExists('hotel_activities');
    }

};
