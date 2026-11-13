<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HousekeepingController extends Controller
{

public function index(Request $request)
{
    $selectedDate = $request->input('date', today()->toDateString());
    $rooms = Room::orderBy('number')->get();

    // 1. Calcul de la semaine (Lundi au Dimanche) autour de la date choisie
    $weekStart = Carbon::parse($selectedDate)->startOfWeek(Carbon::MONDAY);
    $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

    $weekDays = [];
    for ($i = 0; $i < 7; $i++) {
        $weekDays[] = $weekStart->copy()->addDays($i);
    }

    // 2. Agents d'entretien actifs
    $agents = DB::table('housekeepers')
        ->where('is_active', true)
        ->orderBy('full_name')
        ->get();

    // 3. Programmation de toute la semaine (indexée par "room_id_YYYY-MM-DD")
    $weekSchedulesRaw = DB::table('housekeeping_schedules')
        ->join('housekeepers', 'housekeeping_schedules.housekeeper_id', '=', 'housekeepers.id')
        ->whereBetween('housekeeping_schedules.work_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
        ->select(
            'housekeeping_schedules.*',
            'housekeepers.full_name as housekeeper_name'
        )
        ->get();

    $weekMatrix = [];
    foreach ($weekSchedulesRaw as $item) {
        $weekMatrix[$item->room_id . '_' . $item->work_date] = $item;
    }

    // 4. Programmation du jour sélectionné (pour les cartes de chambres)
    $todaySchedules = $weekSchedulesRaw->where('work_date', $selectedDate)->keyBy('room_id');

    // 5. Chambres occupées déjà nettoyées aujourd'hui
    $dailyCleanedRoomIds = DB::table('housekeeping_logs')
        ->where('action_type', 'daily_stay_clean')
        ->whereDate('created_at', today())
        ->pluck('room_id')
        ->toArray();

    // 6. Incidents actifs
    $activeIncidents = DB::table('housekeeping_logs')
        ->where('action_type', 'maintenance_report')
        ->orderByDesc('created_at')
        ->get()
        ->unique('room_id')
        ->keyBy('room_id');

    // 7. Journal de traçabilité
    $recentLogs = DB::table('housekeeping_logs')
        ->join('rooms', 'housekeeping_logs.room_id', '=', 'rooms.id')
        ->leftJoin('users', 'housekeeping_logs.user_id', '=', 'users.id')
        ->select(
            'housekeeping_logs.*',
            'rooms.number as room_number',
            'users.name as supervisor_name'
        )
        ->orderByDesc('housekeeping_logs.created_at')
        ->limit(25)
        ->get();

    $stats = [
        'cleaning'    => $rooms->where('status', 'cleaning')->count(),
        'available'   => $rooms->where('status', 'available')->count(),
        'occupied'    => $rooms->where('status', 'occupied')->count(),
        'maintenance' => $rooms->where('status', 'maintenance')->count(),
    ];

    return view('housekeeping.index', compact(
        'rooms',
        'agents',
        'todaySchedules',
        'selectedDate',
        'weekStart',
        'weekEnd',
        'weekDays',
        'weekMatrix',
        'stats',
        'dailyCleanedRoomIds',
        'activeIncidents',
        'recentLogs'
    ));
}

/**
 * Enregistrer ou mettre à jour tout le planning hebdomadaire d'un seul coup
 */
public function assignWeek(Request $request)
{
    $assignments = $request->input('assignments', []); // format: [room_id][date] = housekeeper_id

    DB::transaction(function () use ($assignments) {
        foreach ($assignments as $roomId => $days) {
            foreach ($days as $date => $housekeeperId) {
                if (empty($housekeeperId)) {
                    // Si "-- Aucun --" est choisi, on retire l'attribution de ce jour
                    DB::table('housekeeping_schedules')
                        ->where('room_id', $roomId)
                        ->where('work_date', $date)
                        ->delete();
                } else {
                    DB::table('housekeeping_schedules')->updateOrInsert(
                        [
                            'room_id'   => $roomId,
                            'work_date' => $date,
                        ],
                        [
                            'housekeeper_id' => $housekeeperId,
                            'task_type'      => 'daily',
                            'status'         => 'pending',
                            'updated_at'     => now(),
                            'created_at'     => now(),
                        ]
                    );
                }
            }
        }
    });

    return back()->with('success', 'Planning hebdomadaire enregistré avec succès.');
}


    /**
     * Ajouter une femme de chambre / agent d'entretien
     */
    public function storeAgent(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:191',
            'phone'     => 'nullable|string|max:50',
        ]);

        DB::table('housekeepers')->insert([
            'full_name'  => $validated['full_name'],
            'phone'      => $validated['phone'] ?? null,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', "Agent d'entretien {$validated['full_name']} ajouté à l'équipe.");
    }

    /**
     * Assigner une chambre à un agent pour la programmation du jour
     */
    public function assignRoom(Request $request)
    {
        $validated = $request->validate([
            'work_date'      => 'required|date',
            'room_id'        => 'required|exists:rooms,id',
            'housekeeper_id' => 'required|exists:housekeepers,id',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        $taskType = ($room->status === 'cleaning') ? 'checkout' : 'daily';

        DB::table('housekeeping_schedules')->updateOrInsert(
            [
                'work_date' => $validated['work_date'],
                'room_id'   => $room->id,
            ],
            [
                'housekeeper_id' => $validated['housekeeper_id'],
                'task_type'      => $taskType,
                'status'         => 'pending',
                'updated_at'     => now(),
                'created_at'     => now(),
            ]
        );

        return back()->with('success', "Chambre {$room->number} attribuée dans le planning du jour.");
    }

    /**
     * Valider le nettoyage après départ (enregistre l'agent responsable)
     */
    public function markAsClean(Request $request, Room $room)
    {
        if ($room->status === 'occupied') {
            return back()->with('error', "Action impossible : la Chambre {$room->number} est actuellement occupée.");
        }

        // Retrouver l'agent assigné aujourd'hui ou celui sélectionné dans le formulaire
        $agentName = $request->input('housekeeper_name');
        if (!$agentName) {
            $schedule = DB::table('housekeeping_schedules')
                ->join('housekeepers', 'housekeeping_schedules.housekeeper_id', '=', 'housekeepers.id')
                ->whereDate('work_date', today())
                ->where('room_id', $room->id)
                ->first();
            $agentName = $schedule->full_name ?? 'Non assigné';
        }

        DB::transaction(function () use ($room, $agentName) {
            $room->update(['status' => 'available']);

            // Clôturer la tâche dans le planning du jour si elle existe
            DB::table('housekeeping_schedules')
                ->whereDate('work_date', today())
                ->where('room_id', $room->id)
                ->update([
                    'status'       => 'done',
                    'completed_at' => now(),
                    'updated_at'   => now(),
                ]);

            DB::table('housekeeping_logs')->insert([
                'room_id'          => $room->id,
                'user_id'          => Auth::id(),
                'housekeeper_name' => $agentName,
                'action_type'      => 'checkout_clean',
                'notes'            => "Nettoyage complet après départ validé (Agent : {$agentName}).",
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        });

        return back()->with('success', "Chambre {$room->number} validée propre par {$agentName}.");
    }

    /**
     * Valider la recouche quotidienne (enregistre l'agent responsable)
     */
    public function markDailyClean(Request $request, Room $room)
    {
        if ($room->status !== 'occupied') {
            return back()->with('error', "La recouche s'applique uniquement aux chambres occupées.");
        }

        $schedule = DB::table('housekeeping_schedules')
            ->join('housekeepers', 'housekeeping_schedules.housekeeper_id', '=', 'housekeepers.id')
            ->whereDate('work_date', today())
            ->where('room_id', $room->id)
            ->first();

        $agentName = $request->input('housekeeper_name') ?? ($schedule->full_name ?? 'Non assigné');

        DB::transaction(function () use ($room, $agentName) {
            DB::table('housekeeping_schedules')
                ->whereDate('work_date', today())
                ->where('room_id', $room->id)
                ->update([
                    'status'       => 'done',
                    'completed_at' => now(),
                    'updated_at'   => now(),
                ]);

            DB::table('housekeeping_logs')->insert([
                'room_id'          => $room->id,
                'user_id'          => Auth::id(),
                'housekeeper_name' => $agentName,
                'action_type'      => 'daily_stay_clean',
                'notes'            => "Recouche quotidienne effectuée (Agent : {$agentName}).",
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        });

        return back()->with('success', "Recouche enregistrée pour la Chambre {$room->number} (Agent : {$agentName}).");
    }

    public function reportIncident(Request $request, Room $room)
    {
        if ($room->status === 'occupied') {
            return back()->with('error', "Impossible de bloquer en maintenance une chambre occupée.");
        }

        $validated = $request->validate(['notes' => 'required|string|max:500']);

        DB::transaction(function () use ($room, $validated) {
            $room->update(['status' => 'maintenance']);
            DB::table('housekeeping_logs')->insert([
                'room_id'     => $room->id,
                'user_id'     => Auth::id(),
                'action_type' => 'maintenance_report',
                'notes'       => $validated['notes'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        });

        return back()->with('success', "Incident signalé sur la Chambre {$room->number}.");
    }

    public function resolveMaintenance(Room $room)
    {
        DB::transaction(function () use ($room) {
            $room->update(['status' => 'cleaning']);
            DB::table('housekeeping_logs')->insert([
                'room_id'     => $room->id,
                'user_id'     => Auth::id(),
                'action_type' => 'maintenance_resolved',
                'notes'       => 'Réparation terminée. Chambre placée en attente de nettoyage.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        });

        return back()->with('success', "Maintenance terminée sur la Chambre {$room->number}.");
    }
public function undoDailyClean(Room $room)
{
    DB::transaction(function () use ($room) {
        // Supprimer l'enregistrement de recouche d'aujourd'hui pour cette chambre
        DB::table('housekeeping_logs')
            ->where('room_id', $room->id)
            ->where('action_type', 'daily_stay_clean')
            ->whereDate('created_at', today())
            ->delete();

        // Remettre la tâche du planning du jour en 'pending'
        DB::table('housekeeping_schedules')
            ->where('room_id', $room->id)
            ->whereDate('work_date', today())
            ->update([
                'status'       => 'pending',
                'completed_at' => null,
                'updated_at'   => now(),
            ]);
    });

    return back()->with('success', "Validation annulée : la Chambre {$room->number} est de nouveau en attente de recouche.");
}

/**
 * Rapport & Audit des entretiens sur une période donnée (Enquête, qualité, performance)
 */
public function report(Request $request)
{
    $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
    $dateTo   = $request->input('date_to', now()->toDateString());
    $roomId   = $request->input('room_id');
    $agent    = $request->input('housekeeper_name');
    $type     = $request->input('action_type');

    $rooms  = Room::orderBy('number')->get();
    $agents = DB::table('housekeepers')->orderBy('full_name')->get();

    $query = DB::table('housekeeping_logs')
        ->join('rooms', 'housekeeping_logs.room_id', '=', 'rooms.id')
        ->leftJoin('users', 'housekeeping_logs.user_id', '=', 'users.id')
        ->whereDate('housekeeping_logs.created_at', '>=', $dateFrom)
        ->whereDate('housekeeping_logs.created_at', '<=', $dateTo)
        ->select(
            'housekeeping_logs.*',
            'rooms.number as room_number',
            'rooms.type as room_type',
            'users.name as supervisor_name'
        );

    if (!empty($roomId)) {
        $query->where('housekeeping_logs.room_id', $roomId);
    }

    if (!empty($agent)) {
        $query->where('housekeeping_logs.housekeeper_name', $agent);
    }

    if (!empty($type)) {
        $query->where('housekeeping_logs.action_type', $type);
    }

    $logs = $query->orderByDesc('housekeeping_logs.created_at')->get();

    // Synthèse de performance par agent sur la période
    $agentSummary = $logs->whereNotNull('housekeeper_name')
        ->groupBy('housekeeper_name')
        ->map(function ($items, $name) {
            return [
                'name'           => $name,
                'total'          => $items->count(),
                'checkout_clean' => $items->where('action_type', 'checkout_clean')->count(),
                'daily_clean'    => $items->where('action_type', 'daily_stay_clean')->count(),
            ];
        })
        ->sortByDesc('total');

    return view('housekeeping.report', compact(
        'logs',
        'rooms',
        'agents',
        'agentSummary',
        'dateFrom',
        'dateTo',
        'roomId',
        'agent',
        'type'
    ));
}



}
