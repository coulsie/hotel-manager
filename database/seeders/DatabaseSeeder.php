<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\MagneticCard;
use App\Models\RoomAccessLog;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use App\Models\Show;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création de l'utilisateur Administrateur par défaut
        User::create([
            'name' => 'Admin',
            'email' => 'admin@hotel.com',
            'password' => bcrypt('password'),
        ]);

        // 2. Création de 10 chambres d'hôtel (Chambres Occitanes)
        for ($i = 101; $i <= 110; $i++) {
            $isOccupied = ($i == 102 || $i == 104 || $i == 107); // 3 chambres occupées pour le test
            Room::create([
                'number' => 'Chambre ' . $i,
                'type' => $i % 2 == 0 ? 'Luxe' : 'Standard',
                'status' => $isOccupied ? 'occupied' : 'available',
                'price_per_night' => $i % 2 == 0 ? 75000 : 45000,
            ]);
        }

        // 3. Génération de 5 cartes RFID liées aux chambres occupées ou disponibles
        $room104 = Room::where('number', 'Chambre 104')->first();
        $room102 = Room::where('number', 'Chambre 102')->first();

        $card1 = MagneticCard::create([
            'card_uid' => 'RFID_98X76A',
            'security_token' => Str::random(32),
            'room_id' => $room104->id,
            'expires_at' => now()->addDays(3),
            'is_active' => true,
        ]);

        $card2 = MagneticCard::create([
            'card_uid' => 'RFID_44B12C',
            'security_token' => Str::random(32),
            'room_id' => $room102->id,
            'expires_at' => now()->addDays(1),
            'is_active' => true,
        ]);

        // 4. Génération de quelques fausses tentatives d'accès pour l'IoT
        RoomAccessLog::create([
            'room_id' => $room104->id,
            'magnetic_card_id' => $card1->id,
            'action' => 'access_granted',
            'scanned_at' => now()->subMinutes(15),
        ]);

        RoomAccessLog::create([
            'room_id' => $room102->id,
            'magnetic_card_id' => null, // Simulation d'une mauvaise carte
            'action' => 'access_denied',
            'scanned_at' => now()->subMinutes(30),
        ]);

        // 5. Création des articles de menu (Restaurant et Bar Piscine)
        MenuItem::create([
            'name' => 'Garba de Luxe au Thon',
            'description' => 'Attiéké local avec darne de thon frit, piments et oignons frais.',
            'price' => 3500,
            'location_type' => 'restaurant',
            'is_available' => true,
        ]);

        MenuItem::create([
            'name' => 'Cocktail l\'Abidjanaise',
            'description' => 'Mélange rafraîchissant à base de fruits exotiques et de liqueur locale.',
            'price' => 5000,
            'location_type' => 'bar_piscine',
            'is_available' => true,
        ]);

        // 6. Création des tables physiques et des transats
        RestaurantTable::create(['number' => 'Table 5', 'location_type' => 'restaurant', 'status' => 'free']);
        RestaurantTable::create(['number' => 'Transat B2', 'location_type' => 'bar_piscine', 'status' => 'occupied']);

        // 7. Création d'un spectacle pour ce soir
        Show::create([
            'title' => 'Grand Concert Concert d\'Ouverture',
            'description' => 'Spectacle musical en direct dans la grande salle de l\'hôtel.',
            'performance_date' => now()->setTime(20, 0, 0),
            'total_capacity' => 200,
            'ticket_price' => 15000,
        ]);
    }
}
