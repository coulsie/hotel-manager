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
    Schema::create('billings', function (Blueprint $table) {
        $table->id();
        $table->string('receipt_number')->unique(); // Ex: REC-20261103-0001

        // Liens optionnels vers vos différentes origines de paiement
        $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('set null');
        $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null'); // Restaurant

        // Pour les spectacles : On stockera ici l'id de la future table d'achat ou le ticket directement
        $table->foreignId('event_ticket_id')->nullable()->constrained('event_tickets')->onDelete('set null');

        // Informations financières
        $table->string('service_type'); // 'room_booking', 'restaurant', 'show_ticket'
        $table->decimal('amount_paid', 10, 2);
        $table->string('payment_method'); // 'cash', 'card', 'mobile_money', 'room_charge'
        $table->string('status')->default('completed'); // 'completed', 'refunded'

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
