<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('conference_spaces')) {
            Schema::create('conference_spaces', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('category', ['reunion', 'seminaire', 'coworking'])->default('reunion');
                $table->unsignedInteger('capacity')->default(10);
                $table->decimal('hourly_rate', 10, 2)->default(0);
                $table->decimal('half_day_rate', 10, 2)->default(0);
                $table->decimal('full_day_rate', 10, 2)->default(0);
                $table->text('equipments')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            DB::table('conference_spaces')->insert([
                [
                    'name'          => 'Salle Exécutive Lagune',
                    'category'      => 'reunion',
                    'capacity'      => 16,
                    'hourly_rate'   => 15000,
                    'half_day_rate' => 50000,
                    'full_day_rate' => 90000,
                    'equipments'    => 'Écran TV 75", Visioconférence, Wi-Fi Fibre, Tableau blanc',
                    'is_active'     => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ],
                [
                    'name'          => 'Grand Auditorium Ébène',
                    'category'      => 'seminaire',
                    'capacity'      => 80,
                    'hourly_rate'   => 40000,
                    'half_day_rate' => 150000,
                    'full_day_rate' => 250000,
                    'equipments'    => 'Vidéoprojecteur HD, Sonorisation + 2 micros sans fil, Climatisation, Estrade',
                    'is_active'     => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ],
                [
                    'name'          => 'Espace Coworking Horizon',
                    'category'      => 'coworking',
                    'capacity'      => 25,
                    'hourly_rate'   => 2500,
                    'half_day_rate' => 8000,
                    'full_day_rate' => 15000,
                    'equipments'    => 'Poste individuel, Fibre optique haut débit, Prises électriques, Accès café',
                    'is_active'     => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ],
            ]);
        }

        if (!Schema::hasTable('space_bookings')) {
            Schema::create('space_bookings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('space_id');
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('room_id')->nullable();
                $table->string('client_name');
                $table->string('client_phone')->nullable();
                $table->date('booking_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->enum('pricing_type', ['hourly', 'half_day', 'full_day'])->default('full_day');
                $table->unsignedInteger('attendees')->default(1);
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->decimal('amount_paid', 10, 2)->default(0);
                $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
                $table->enum('status', ['confirmed', 'in_progress', 'completed', 'cancelled'])->default('confirmed');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('space_bookings');
        Schema::dropIfExists('conference_spaces');
    }
};
