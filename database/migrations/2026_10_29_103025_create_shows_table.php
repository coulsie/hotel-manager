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
        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('performance_date'); // Date et heure du spectacle
            $table->integer('total_capacity');    // Nombre de places maximum
            $table->timestamps();
            $table->decimal('ticket_price', 8, 2)->nullable(); // ->nullable() permet de ne pas saisir de prix

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shows');
    }
};
