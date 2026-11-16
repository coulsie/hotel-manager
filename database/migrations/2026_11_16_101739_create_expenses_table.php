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
    Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->string('voucher_number')->unique(); // Ex: DEP-20261116-001
        $table->string('category');       // 'entretien', 'carburant', 'cuisine', 'maintenance', 'fournitures', 'autre'
        $table->string('description');    // Motif précis de la dépense
        $table->decimal('amount', 10, 2);
        $table->string('payment_method')->default('Espèces'); // Espèces (impacte le tiroir), Wave, Orange Money, Virement
        $table->string('beneficiary')->nullable(); // Fournisseur ou agent ayant reçu les fonds
        $table->string('reference')->nullable();   // N° facture ou reçu fournisseur
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        $table->date('expense_date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
