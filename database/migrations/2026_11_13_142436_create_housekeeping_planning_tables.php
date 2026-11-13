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
    // 1. Liste des agents d'entretien / femmes de chambre
    Schema::create('housekeepers', function (Blueprint $table) {
        $table->id();
        $table->string('full_name');
        $table->string('phone')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    // 2. Programmation journalière (Qui nettoie quelle chambre aujourd'hui)
    Schema::create('housekeeping_schedules', function (Blueprint $table) {
        $table->id();
        $table->date('work_date');
        $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
        $table->foreignId('housekeeper_id')->constrained('housekeepers')->onDelete('cascade');
        $table->string('task_type')->default('daily'); // 'checkout' (départ) ou 'daily' (recouche)
        $table->string('status')->default('pending');  // 'pending' (à faire) ou 'done' (terminé)
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();

        $table->unique(['work_date', 'room_id']); // Une chambre n'est programmée qu'une fois par jour
    });

    // 3. Ajouter le nom de l'agent exécutant dans le journal existant
    Schema::table('housekeeping_logs', function (Blueprint $table) {
        $table->string('housekeeper_name')->nullable()->after('user_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housekeeping_planning_tables');
    }
};
