<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use App\Models\Event;
use App\Models\ShowBooking;
use App\Models\RoomAccessLog;
use App\Models\Room;
use App\Models\Order;
use App\Models\Billing;
use App\Models\ExtraService;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistiques réelles des Chambres
        $totalRooms     = Room::count();
        $occupiedRooms  = Room::where('status', 'occupied')->count();
        $availableRooms = Room::where('status', 'available')->count();
        $cleaningRooms  = Room::where('status', 'cleaning')->count();

        $roomsStats     = "$occupiedRooms / $totalRooms";
        $occupancyRate  = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        // 2. Chiffre d'affaires encaissé du jour (Tous pôles confondus)
        $todayBillings = Billing::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->get();

        $lodgingRevenue    = $todayBillings->where('service_type', 'room_booking')->sum('amount_paid');
        $restaurantRevenue = $todayBillings->where('service_type', 'restaurant')->sum('amount_paid');
        $pressingRevenue   = $todayBillings->where('service_type', 'pressing')->sum('amount_paid');
        $carWashRevenue    = $todayBillings->where('service_type', 'car_wash')->sum('amount_paid');
        $ticketRevenue     = $todayBillings->where('service_type', 'show_ticket')->sum('amount_paid');

        $totalTodayRevenue = $todayBillings->sum('amount_paid');
        $revenueStats      = number_format($totalTodayRevenue, 0, ',', ' ') . ' F';

        // 3. Nombre de scans IoT lors des dernières 24 heures
        $iotScansCount = RoomAccessLog::where('scanned_at', '>=', now()->subDay())->count();
        $iotStats      = "$iotScansCount scans";

        // 4. Taux de remplissage du prochain spectacle
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

        // 6. Données des 7 derniers jours pour la courbe d'évolution financière multi-pôles
        $chartLabels    = [];
        $lodgingData    = [];
        $restaurantData = [];
        $pressingData   = [];
        $carWashData    = [];
        $ticketsData    = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d/m');

            $dayBillings = Billing::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->get();

            $lodgingData[]    = $dayBillings->where('service_type', 'room_booking')->sum('amount_paid');
            $restaurantData[] = $dayBillings->where('service_type', 'restaurant')->sum('amount_paid');
            $pressingData[]   = $dayBillings->where('service_type', 'pressing')->sum('amount_paid');
            $carWashData[]    = $dayBillings->where('service_type', 'car_wash')->sum('amount_paid');
            $ticketsData[]    = $dayBillings->where('service_type', 'show_ticket')->sum('amount_paid');
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
            'lodgingRevenue',
            'restaurantRevenue',
            'pressingRevenue',
            'carWashRevenue',
            'ticketRevenue',
            'upcomingEvents',
            'chartLabels',
            'lodgingData',
            'restaurantData',
            'pressingData',
            'carWashData',
            'ticketsData'
        ));
    }
}
