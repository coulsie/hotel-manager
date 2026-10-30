<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shows', function (Blueprint $table) {
            // Suppression des colonnes qui migrent vers la table events
            if (Schema::hasColumn('shows', 'performance_date')) {
                $table->dropColumn('performance_date');
            }
            if (Schema::hasColumn('shows', 'ticket_price')) {
                $table->dropColumn('ticket_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->datetime('performance_date')->nullable();
            $table->decimal('ticket_price', 10, 2)->nullable();
        });
    }
};
