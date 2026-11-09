<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('show_bookings', function (Blueprint $table) {
            // Ajoute la colonne event_id juste après la colonne show_id
            $table->foreignId('event_id')->nullable()->after('show_id')->constrained('events')->onDelete('cascade');

            // Si la table show_bookings contient un champ customer_name, on s'assure qu'il est là
            if (!Schema::hasColumn('show_bookings', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('show_bookings', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
        });
    }
};
