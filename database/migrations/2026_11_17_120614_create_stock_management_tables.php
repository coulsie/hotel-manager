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
    // 1. Catalogue des articles en stock (Économat)
    Schema::create('stock_items', function (Blueprint $table) {
        $table->id();
        $table->string('category'); // 'bar', 'cuisine', 'entretien', 'linge', 'maintenance'
        $table->string('name');     // Ex: "Eau minérale 1.5L", "Bouteille Gaz B12", "Eau de Javel 5L", "Drap 2 places"
        $table->string('unit')->default('unité'); // 'bouteille', 'carton', 'kg', 'litre', 'pièce'
        $table->decimal('current_stock', 10, 2)->default(0);
        $table->decimal('alert_threshold', 10, 2)->default(5); // Seuil d'alerte minimum
        $table->decimal('unit_cost', 10, 2)->default(0);       // Prix d'achat moyen unitaire
        $table->timestamps();
    });

    // 2. Journal des mouvements (Entrées, Sorties, Pertes / Inventaire)
    Schema::create('stock_movements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('stock_item_id')->constrained('stock_items')->onDelete('cascade');
        $table->enum('type', ['in', 'out', 'loss']); // 'in' = Entrée/Achat, 'out' = Sortie service, 'loss' = Casse/Perte
        $table->decimal('quantity', 10, 2);
        $table->string('department')->nullable();    // 'Bar Piscine', 'Cuisine', 'Gouvernance', 'Réception'
        $table->string('reason')->nullable();        // Motif ou N° Bon de livraison
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('stock_movements');
    Schema::dropIfExists('stock_items');
}

};
