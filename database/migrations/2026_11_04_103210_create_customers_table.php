

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name'); // Nom complet du client
            $table->string('phone')->nullable(); // Utile pour les suivis de paiements mobiles
            $table->string('id_card_number')->nullable(); // Pièce d'identité (CNI / Passeport)
            $table->timestamps();
        });

        // Liaison de la réservation ou de l'occupation à la table rooms ou billings si nécessaire
        // Par exemple, ajout d'une clé étrangère temporaire sur les réservations (si gérées dans la table rooms ou une table pivot)
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
