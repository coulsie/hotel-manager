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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_table_id')->constrained()->onDelete('cascade');
            // Optionnel : Relié à une chambre si le client demande "Facturer sur la chambre"
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('status')->default('pending'); // 'pending', 'paid', 'charged_to_room'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
