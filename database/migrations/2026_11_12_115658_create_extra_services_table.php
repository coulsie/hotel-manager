<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extra_services', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['pressing', 'car_wash']);
            $table->string('service_name');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 10, 2);

            // Clé étrangère vers la table 'rooms' (nullable pour les clients externes)
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();

            // Nom du client externe ou immatriculation du véhicule
            $table->string('customer_name')->nullable();

            $table->enum('work_status', ['pending', 'ready'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_services');
    }
};
