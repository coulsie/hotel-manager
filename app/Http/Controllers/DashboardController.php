<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\ShowBooking;
use App\Models\RoomAccessLog;
use App\Models\Room;
use App\Models\Order;
use App\Models\Billing;
use App\Models\ExtraService;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. Statistiques réelles des Chambres & Gouvernance
        $totalRooms       = Room::count();
        $occupiedRooms    = Room::where('status', 'occupied')->count();
        $availableRooms   = Room::where('status', 'available')->count();
        $cleaningRooms    = Room::where('status', 'cleaning')->count();
        $maintenanceRooms = Room::where('status', 'maintenance')->count();

        $roomsStats       = "$occupiedRooms / $totalRooms";
        $operationalRooms = max(1, $totalRooms - $maintenanceRooms);
        $occupancyRate    = $totalRooms > 0 ? round(($occupiedRooms / $operationalRooms) * 100, 1) : 0;

        // 2. Chiffre d'affaires encaissé du jour (Tous pôles confondus, y compris arrhes et nouvelles activités)
        $todayBillings = Billing::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->get();

        $lodgingRevenue    = $todayBillings->whereIn('service_type', ['room_booking', 'reservation_deposit'])->sum('amount_paid');
        $restaurantRevenue = $todayBillings->where('service_type', 'restaurant')->sum('amount_paid');
        $pressingRevenue   = $todayBillings->where('service_type', 'pressing')->sum('amount_paid');
        $carWashRevenue    = $todayBillings->where('service_type', 'car_wash')->sum('amount_paid');
        $ticketRevenue     = $todayBillings->where('service_type', 'show_ticket')->sum('amount_paid');
        $activitiesRevenue = $todayBillings->whereIn('service_type', ['piscine', 'spa', 'coworking', 'seminaire', 'navette', 'boutique'])->sum('amount_paid');

        $totalTodayRevenue = $todayBillings->sum('amount_paid');
        $revenueStats      = number_format($totalTodayRevenue, 0, ',', ' ') . ' F';

        // 2 bis. Nouveaux KPIs Hôteliers & Trésorerie Nette du Jour
        $revPar = $totalRooms > 0 ? round($lodgingRevenue / $totalRooms) : 0;
        $adr    = $occupiedRooms > 0 ? round($lodgingRevenue / $occupiedRooms) : 0;

        $todayExpenses = DB::table('expenses')
            ->whereDate('expense_date', $today)
            ->sum('amount');

        $todayCashIn = $todayBillings->whereIn('payment_method', ['Espèces', 'cash', 'especes'])->sum('amount_paid');
        $todayCashOut = DB::table('expenses')
            ->whereDate('expense_date', $today)
            ->whereIn('payment_method', ['Espèces', 'cash', 'especes'])
            ->sum('amount');

        $netCashToday = $todayCashIn - $todayCashOut;

        // Chiffre d'affaires mensuel cumulé
        $monthTotalRevenue = Billing::where('status', 'completed')
            ->whereDate('created_at', '>=', $startOfMonth)
            ->sum('amount_paid');

        // 3. Nombre de scans IoT lors des dernières 24 heures
        $iotScansCount = RoomAccessLog::where('scanned_at', '>=', now()->subDay())->count();
        $iotStats      = "$iotScansCount scans";

        // 4. Taux de remplissage du prochain spectacle
        $nextEvent = Event::with(['venue', 'tickets'])
            ->where('performance_date', '>=', $today)
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
        $activitiesData = [];
        $expensesData   = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d/m');

            $dayBillings = Billing::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->get();

            $lodgingData[]    = $dayBillings->whereIn('service_type', ['room_booking', 'reservation_deposit'])->sum('amount_paid');
            $restaurantData[] = $dayBillings->where('service_type', 'restaurant')->sum('amount_paid');
            $pressingData[]   = $dayBillings->where('service_type', 'pressing')->sum('amount_paid');
            $carWashData[]    = $dayBillings->where('service_type', 'car_wash')->sum('amount_paid');
            $ticketsData[]    = $dayBillings->where('service_type', 'show_ticket')->sum('amount_paid');
            $activitiesData[] = $dayBillings->whereIn('service_type', ['piscine', 'spa', 'coworking', 'seminaire', 'navette', 'boutique'])->sum('amount_paid');
            $expensesData[]   = DB::table('expenses')->whereDate('expense_date', $date)->sum('amount');
        }

        // 7. Liste des prochains concerts pour le tableau de suivi
        $upcomingEvents = Event::with(['venue', 'tickets'])
            ->where('performance_date', '>=', now())
            ->orderBy('performance_date', 'asc')
            ->take(4)
            ->get();

               // 8. Mouvements Réception du jour (Arrivées & Départs prévus)
        $arrivalsToday = Reservation::with(['customer', 'room'])
            ->where('status', 'confirmed')
            ->whereDate('check_in_date', $today)
            ->get();

             // Départs issus des réservations (en cours 'checked_in' ou 'confirmed' dont la sortie est aujourd'hui ou dépassée)
        $departuresToday = Reservation::with(['customer', 'room'])
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->whereDate('check_out_date', '<=', $today)
            ->get();

        // Ajouter les éventuels check-ins directs (sans réservation préalable) dont la carte magnétique expire aujourd'hui
        $resaRoomIds = $departuresToday->pluck('room_id')->toArray();
        $directExpiringCards = DB::table('magnetic_cards')
            ->join('rooms', 'magnetic_cards.room_id', '=', 'rooms.id')
            ->where('magnetic_cards.is_active', 1)
            ->where('rooms.status', 'occupied')
            ->whereNotIn('rooms.id', $resaRoomIds)
            ->whereDate('magnetic_cards.expires_at', '<=', $today)
            ->select('rooms.id as room_id', 'magnetic_cards.expires_at')
            ->get();

        foreach ($directExpiringCards as $cardDep) {
            $virtualRes = new Reservation([
                'room_id'        => $cardDep->room_id,
                'check_out_date' => $cardDep->expires_at,
                'status'         => 'checked_in',
            ]);
            $virtualRes->setRelation('room', Room::find($cardDep->room_id));
            $virtualRes->setRelation('customer', new \App\Models\Customer(['full_name' => 'Client Séjour Direct']));
            $departuresToday->push($virtualRes);
        }

        // 8 bis. Planning des Arrivées & Départs de la semaine (Aujourd'hui + 6 prochains jours)
        $weekStart = Carbon::today();
        $weekEnd   = Carbon::today()->addDays(6);

        $weekReservations = Reservation::with(['customer', 'room'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where(function ($q) use ($weekStart, $weekEnd) {
                $q->whereBetween('check_in_date', [$weekStart, $weekEnd])
                  ->orWhereBetween('check_out_date', [$weekStart, $weekEnd]);
            })
            ->get();

        $weekDirectCards = DB::table('magnetic_cards')
            ->join('rooms', 'magnetic_cards.room_id', '=', 'rooms.id')
            ->where('magnetic_cards.is_active', 1)
            ->where('rooms.status', 'occupied')
            ->whereBetween(DB::raw('DATE(magnetic_cards.expires_at)'), [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->select('rooms.id as room_id', 'magnetic_cards.expires_at')
            ->get();

        $weeklyMovements = [];
        for ($d = 0; $d < 7; $d++) {
            $dayDate = $weekStart->copy()->addDays($d);
            $dayStr  = $dayDate->toDateString();

            $dayArrivals = $weekReservations->filter(
                fn($r) => $r->status === 'confirmed' && Carbon::parse($r->check_in_date)->toDateString() === $dayStr
            );

            $dayDepartures = $weekReservations->filter(
                fn($r) => in_array($r->status, ['checked_in', 'confirmed']) && Carbon::parse($r->check_out_date)->toDateString() === $dayStr
            )->values();

            // Ajouter les départs de check-ins directs prévus ce jour-là
            $dayResRoomIds = $dayDepartures->pluck('room_id')->toArray();
            foreach ($weekDirectCards as $wCard) {
                if (!in_array($wCard->room_id, $dayResRoomIds) && Carbon::parse($wCard->expires_at)->toDateString() === $dayStr) {
                    $vRes = new Reservation([
                        'room_id'        => $wCard->room_id,
                        'check_out_date' => $wCard->expires_at,
                        'status'         => 'checked_in',
                    ]);
                    $vRes->setRelation('room', Room::find($wCard->room_id));
                    $vRes->setRelation('customer', new \App\Models\Customer(['full_name' => 'Client Séjour Direct']));
                    $dayDepartures->push($vRes);
                }
            }

            $weeklyMovements[] = [
                'date_iso'   => $dayStr,
                'label'      => $d === 0 ? "Aujourd'hui (" . $dayDate->format('d/m') . ")" : ucfirst($dayDate->locale('fr')->isoFormat('dddd DD/MM')),
                'arrivals'   => $dayArrivals,
                'departures' => $dayDepartures,
            ];
        }

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
            'maintenanceRooms',
            'occupancyRate',
            'lodgingRevenue',
            'restaurantRevenue',
            'pressingRevenue',
            'carWashRevenue',
            'ticketRevenue',
            'activitiesRevenue',
            'totalTodayRevenue',
            'todayExpenses',
            'netCashToday',
            'revPar',
            'adr',
            'monthTotalRevenue',
            'upcomingEvents',
            'arrivalsToday',
            'departuresToday',
            'weeklyMovements',
            'chartLabels',
            'lodgingData',
            'restaurantData',
            'pressingData',
            'carWashData',
            'ticketsData',
            'activitiesData',
            'expensesData'
        ));
    }
}
