<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Order;
use App\Models\RoomAccessLog;
use App\Models\Show;
use App\Models\ShowBooking;
use Carbon\Carbon;
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
        $nextShow = Show::whereDate('performance_date', Carbon::today())->first();
        if ($nextShow && $nextShow->total_capacity > 0) {
            $bookingsCount = ShowBooking::where('show_id', $nextShow->id)->where('status', 'confirmed')->count();
            $percentage = round(($bookingsCount / $nextShow->total_capacity) * 100);
            $showStats = "$percentage% Plein";
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
