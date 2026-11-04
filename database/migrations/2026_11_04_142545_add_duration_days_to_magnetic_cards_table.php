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
        Schema::table('magnetic_cards', function (Blueprint $table) {
            // Ajoute le champ pour bloquer le nombre de nuitées vendues
            $table->integer('duration_days')->default(1)->after('room_id');
        });
    }

    public function down(): void
    {
        Schema::table('magnetic_cards', function (Blueprint $table) {
            $table->dropColumn('duration_days');
        });
    }

};
