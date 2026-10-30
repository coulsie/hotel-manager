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
    Schema::create('magnetic_cards', function (Blueprint $table) {
        $table->id();
        $table->string('card_uid', 50)->unique(); // L'identifiant physique RFID de la carte
        $table->string('security_token', 100);    // Un jeton chiffré unique
        $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null'); // Liaison avec la chambre
        $table->timestamp('expires_at')->nullable(); // Date de fin du séjour (expiration de la carte)
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magnetic_cards');
    }
};
