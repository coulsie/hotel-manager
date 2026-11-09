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
        Schema::table('show_bookings', function (Blueprint $table) {
            // Modifie la colonne user_id pour lui permettre de recevoir la valeur NULL
            $table->foreignId('user_id')->nullable()->change();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('show_bookings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};

