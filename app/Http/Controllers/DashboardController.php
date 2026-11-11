<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use App\Models\Event;
use App\Models\ShowBooking;
use App\Models\RoomAccessLog;
use App\Models\Room;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistiques réelles des Chambres (sans fausses valeurs de secours)
        $totalRooms     = Room::count();
        $occupiedRooms  = Room::where('status', 'occupied')->count();
        $availableRooms = Room::where('status', 'available')->count();
        $cleaningRooms  = Room::where('status', 'cleaning')->count();

        $roomsStats     = "$occupiedRooms / $totalRooms";
        $occupancyRate  = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        // 2. Chiffre d'affaires du jour (Restauration + Billetterie)
        $restaurantRevenue = Order::whereDate('created_at', Carbon::today())
            ->whereIn('status', ['paid', 'charged_to_room'])
            ->sum('total_amount');

        $ticketRevenue = ShowBooking::whereDate('created_at', Carbon::today())
            ->whereIn('status', ['confirmed', 'charged_to_room'])
            ->sum('amount_paid');

        $totalTodayRevenue = $restaurantRevenue + $ticketRevenue;
        $revenueStats = number_format($totalTodayRevenue, 0, ',', ' ') . ' F';

        // 3. Nombre de scans IoT lors des dernières 24 heures
        $iotScansCount = RoomAccessLog::where('scanned_at', '>=', now()->subDay())->count();
        $iotStats = "$iotScansCount scans";

        // 4. Taux de remplissage du prochain spectacle (Relation 'venue' corrigée)
        $nextEvent = Event::with(['venue', 'tickets'])
            ->where('performance_date', '>=', Carbon::today())
            ->orderBy('performance_date', 'asc')
            ->first();

        if ($nextEvent && $nextEvent->venue) {
            $totalCapacity = $nextEvent->venue->total_capacity ?? 0;
            if ($totalCapacity > 0) {
                $bookingsCount = ShowBooking::where('event_id', $nextEvent->id)
                    ->whereIn('status', ['confirmed', 'charged_to_room'])
                    ->count();
                $percentage = round(($bookingsCount / $totalCapacity) * 100);
                $showStats = "$percentage% Plein";
            } else {
                $showStats = "0% Plein";
            }
        } else {
            $showStats = "Aucun concert";
        }

        // 5. Récupération des 10 derniers logs d'accès pour le suivi en direct
        $recentLogs = RoomAccessLog::with(['room', 'magneticCard'])
            ->orderBy('scanned_at', 'desc')
            ->take(10)
            ->get();

        // 6. Données des 7 derniers jours pour la courbe d'évolution financière
        $chartLabels = [];
        $chartRestoData = [];
        $chartTicketData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d/m');

            $chartRestoData[] = Order::whereDate('created_at', $date)
                ->whereIn('status', ['paid', 'charged_to_room'])
                ->sum('total_amount');

            $chartTicketData[] = ShowBooking::whereDate('created_at', $date)
                ->whereIn('status', ['confirmed', 'charged_to_room'])
                ->sum('amount_paid');
        }

        // 7. Liste des prochains concerts pour le tableau de suivi
        $upcomingEvents = Event::with(['venue', 'tickets'])
            ->where('performance_date', '>=', now())
            ->orderBy('performance_date', 'asc')
            ->take(4)
            ->get();

        return view('welcome', compact(
            'roomsStats',
            'revenueStats',
            'iotStats',
            'showStats',
            'recentLogs',
            'totalRooms',
            'occupiedRooms',
            'availableRooms',
            'cleaningRooms',
            'occupancyRate',
            'restaurantRevenue',
            'ticketRevenue',
            'upcomingEvents',
            'chartLabels',
            'chartRestoData',
            'chartTicketData'
        ));
    }
}
