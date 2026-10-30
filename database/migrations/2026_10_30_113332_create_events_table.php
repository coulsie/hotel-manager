<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            // Clé étrangère pointant vers l'ID de la table 'shows' (la Salle)
            $table->foreignId('show_id')->constrained('shows')->onDelete('cascade');

            $table->string('title'); // Nom du spectacle (ex: Le Lac des Cygnes)
            $table->text('description')->nullable();
            $table->datetime('performance_date'); // Date de la représentation

            // Prix du ticket rendu NULLABLE (non obligatoire comme demandé)
            $table->decimal('ticket_price', 10, 2)->nullable()->default(0.00);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
