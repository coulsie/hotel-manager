<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Table des Entreprises / Sociétés Partenaires
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('tax_id')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('address')->nullable();
                $table->decimal('negotiated_discount', 5, 2)->default(0);
                $table->timestamps();
            });
        }

        // 2. Enrichissement de la fiche client (uniquement les colonnes manquantes)
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            }
            if (!Schema::hasColumn('customers', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('customers', 'vip_level')) {
                $table->enum('vip_level', ['standard', 'regular', 'vip'])->default('standard')->after('nationality');
            }
            if (!Schema::hasColumn('customers', 'preferences')) {
                $table->text('preferences')->nullable()->after('vip_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
            foreach (['vip_level', 'preferences'] as $col) {
                if (Schema::hasColumn('customers', 'col')) {
                    $table->dropColumn($col);
                }
            }
        });
        Schema::dropIfExists('companies');
    }
};
