<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_catalogs', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['pressing', 'car_wash']);
            $table->string('name'); // ex: Costume 2 pièces, Lavage Complet Berline
            $table->decimal('price', 10, 2); // Montant (ex: 5000.00)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_catalogs');
    }
};
