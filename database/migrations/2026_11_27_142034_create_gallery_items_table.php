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
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50); // chambres, restaurant, piscine, salles, spectacle
            $table->string('title', 191);
            $table->string('description', 255)->nullable();
            $table->string('image');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Pré-remplir avec vos visuels déjà enregistrés dans storage/app/public/
        \Illuminate\Support\Facades\DB::table('gallery_items')->insert([
            ['category' => 'chambres',   'title' => 'Suite VIP & Salon Privé',        'description' => 'Espace salon, bureau exécutif et literie King Size',              'image' => 'rooms/suite-vip.jpg',    'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'chambres',   'title' => 'Chambre Luxe & Confort',         'description' => 'Ambiance feutrée, TV Smart et espace détente',                    'image' => 'rooms/luxe.jpg',         'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'restaurant', 'title' => 'Restaurant Gastronomique',       'description' => 'Cuisine locale et internationale, service continu & room service','image' => 'gallery/restaurant.jpg', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'piscine',    'title' => 'Piscine & Espace Détente',       'description' => 'Bassin extérieur, transats et rafraîchissements au bar',          'image' => 'gallery/piscine.jpg',    'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'salles',     'title' => 'Salle de Conférence & Séminaire','description' => 'Équipement audiovisuel complet, vidéoprojecteur et Wi-Fi',        'image' => 'gallery/seminaire.jpg',  'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'salles',     'title' => 'Espace Coworking & Réunions',    'description' => 'Postes connectés, pauses café et climatisation',                 'image' => 'gallery/coworking.jpg',  'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'spectacle',  'title' => 'Salle de Spectacle & Concerts',  'description' => 'Scène équipée, soirées culturelles et galas',                     'image' => 'gallery/spectacle.jpg',  'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'restaurant', 'title' => 'Hall d\'Accueil & Réception',    'description' => 'Accueil personnalisé 24h/24 et conciergerie',                     'image' => 'gallery/reception.jpg',  'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }
};
