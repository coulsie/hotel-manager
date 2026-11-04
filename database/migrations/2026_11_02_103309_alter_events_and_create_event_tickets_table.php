<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Nettoyage de la table events
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'ticket_price')) {
                $table->dropColumn('ticket_price');
            }
        });

        // 2. Création de la table des tickets par événement
        Schema::create('event_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('ticket_type'); // 'populaire', 'honneurs', 'vip', etc.
            $table->integer('total_capacity'); // Nombre de places max pour ce type
            $table->integer('available_seats'); // Nombre de places restantes
            $table->decimal('price', 10, 2); // Le coût spécifique
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_tickets');
        Schema::table('events', function (Blueprint $table) {
            $table->decimal('ticket_price', 10, 2)->nullable()->default(0.00);
        });
    }
};
