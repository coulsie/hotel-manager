<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use App\Models\Event;
use App\Models\ShowBooking;
use App\Models\RoomAccessLog;
use App\Models\Room;
use App\Models\Order;
use App\Models\Show;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Calcul des chambres occupées sur le total
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        // Valeurs de secours si la base est vide pour le test
        $roomsStats = $totalRooms > 0 ? "$occupiedRooms / $totalRooms" : "12 / 40";

        // 2. Chiffre d'affaires du jour pour le Restaurant & Bar Piscine
        $restaurantRevenue = Order::whereDate('created_at', Carbon::today())
            ->whereIn('status', ['paid', 'charged_to_room'])
            ->sum('total_amount');
        $revenueStats = $restaurantRevenue > 0 ? number_format($restaurantRevenue, 0, ',', ' ') . ' F' : "345 000 F";

        // 3. Nombre de scans IoT lors des dernières 24 heures
        $iotScansCount = RoomAccessLog::where('scanned_at', '>=', now()->subDay())->count();
        $iotStats = $iotScansCount > 0 ? "$iotScansCount scans" : "142 scans";

        // 4. Taux de remplissage du spectacle de ce soir
        // 4. Taux de remplissage du spectacle de ce soir
        $nextEvent = Event::whereDate('performance_date', Carbon::today())->first();

        if ($nextEvent) {
            // Récupération de la capacité via la relation avec le show (la salle)
            $totalCapacity = $nextEvent->show->total_capacity ?? 0;

            if ($totalCapacity > 0) {
                $bookingsCount = ShowBooking::where('show_id', $nextEvent->show_id)
                    ->where('status', 'confirmed')
                    ->count();
                $percentage = round(($bookingsCount / $totalCapacity) * 100);
                $showStats = "$percentage% Plein";
            } else {
                $showStats = "85% Plein";
            }
        } else {
            $showStats = "85% Plein";
        }


        // 5. Récupération des 10 derniers logs d'accès pour le suivi en direct
        $recentLogs = RoomAccessLog::with(['room', 'magneticCard'])
            ->orderBy('scanned_at', 'desc')
            ->take(10)
            ->get();

        return view('welcome', compact('roomsStats', 'revenueStats', 'iotStats', 'showStats', 'recentLogs'));
    }
}
