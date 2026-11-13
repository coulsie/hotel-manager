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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phone');
            $table->string('nationality')->default('Ivoirienne')->after('email');
            $table->string('id_type')->default('CNI')->after('nationality'); // CNI, Passeport, Permis...
            // On conserve votre colonne existante 'id_card_number' !
            $table->string('address')->nullable()->after('id_card_number');
            $table->text('notes')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['email', 'nationality', 'id_type', 'address', 'notes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    
};
