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
    // 1. Planning des Quarts de Travail (Shifts)
    Schema::create('staff_shifts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('department'); // 'Réception', 'Restauration', 'Gouvernance', 'Sécurité', 'Maintenance'
        $table->date('shift_date');
        $table->enum('shift_type', ['matin', 'soir', 'nuit', 'journee'])->default('journee');
        $table->string('hours_label')->nullable(); // Ex: "07h00 - 15h00"
        $table->timestamps();
    });

    // 2. Pointage quotidien des présences
    Schema::create('staff_attendances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->date('work_date');
        $table->time('clock_in')->nullable();
        $table->time('clock_out')->nullable();
        $table->enum('status', ['present', 'late', 'absent', 'off'])->default('present');
        $table->string('note')->nullable();
        $table->timestamps();
    });

    // 3. Avances sur salaire & Acomptes personnel
    Schema::create('staff_advances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->decimal('amount', 10, 2);
        $table->date('advance_date');
        $table->string('payment_method')->default('Espèces');
        $table->string('reason')->nullable();
        $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('staff_advances');
    Schema::dropIfExists('staff_attendances');
    Schema::dropIfExists('staff_shifts');
}

};
