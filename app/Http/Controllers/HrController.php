<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HrController extends Controller
{
    public function index(Request $request)
    {
        $weekStart = $request->filled('week_start')
            ? Carbon::parse($request->input('week_start'))->startOfWeek()
            : Carbon::now()->startOfWeek();

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $weekStart->copy()->addDays($i);
        }
        $weekEnd = $weekStart->copy()->addDays(6);

        // 1. Liste du personnel (comptes utilisateurs)
        $staff = User::with('roles')->orderBy('name')->get();

        // 2. Quarts de travail planifiés sur la semaine affichée
        $shifts = DB::table('staff_shifts')
            ->whereBetween('shift_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get()
            ->groupBy(fn($s) => $s->user_id . '_' . $s->shift_date);

        // 3. Pointages d'aujourd'hui
        $today = Carbon::today()->toDateString();
        $todayAttendances = DB::table('staff_attendances')
            ->whereDate('work_date', $today)
            ->get()
            ->keyBy('user_id');

        // 4. Avances sur salaire du mois en cours
        $monthAdvances = DB::table('staff_advances')
            ->join('users', 'staff_advances.user_id', '=', 'users.id')
            ->whereMonth('staff_advances.advance_date', Carbon::now()->month)
            ->whereYear('staff_advances.advance_date', Carbon::now()->year)
            ->select('staff_advances.*', 'users.name as employee_name')
            ->orderByDesc('staff_advances.advance_date')
            ->get();

        $totalMonthAdvances = $monthAdvances->sum('amount');

        return view('hr.index', compact(
            'staff',
            'days',
            'weekStart',
            'shifts',
            'todayAttendances',
            'monthAdvances',
            'totalMonthAdvances'
        ));
    }

    /**
     * Planifier un quart de travail (Shift) pour un employé
     */
    public function storeShift(Request $request)
    {
        $validated = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'department' => 'required|string|max:50',
            'shift_date' => 'required|date',
            'shift_type' => 'required|in:matin,soir,nuit,journee',
        ]);

        $hoursMap = [
            'matin'   => '07h00 - 15h00',
            'soir'    => '15h00 - 23h00',
            'nuit'    => '23h00 - 07h00',
            'journee' => '08h00 - 17h00',
        ];

        DB::table('staff_shifts')->updateOrInsert(
            [
                'user_id'    => $validated['user_id'],
                'shift_date' => $validated['shift_date'],
            ],
            [
                'department'  => $validated['department'],
                'shift_type'  => $validated['shift_type'],
                'hours_label' => $hoursMap[$validated['shift_type']],
                'updated_at'  => now(),
                'created_at'  => now(),
            ]
        );

        return back()->with('success', 'Quart de travail planifié avec succès.');
    }

    /**
     * Enregistrer le pointage du jour (Présent, Retard, Absent, Repos)
     */
    public function storeAttendance(Request $request)
    {
        $validated = $request->validate([
            'user_id'   => 'required|exists:users,id',
            'status'    => 'required|in:present,late,absent,off',
            'clock_in'  => 'nullable',
            'clock_out' => 'nullable',
            'note'      => 'nullable|string|max:255',
        ]);

        $today = Carbon::today()->toDateString();

        DB::table('staff_attendances')->updateOrInsert(
            [
                'user_id'   => $validated['user_id'],
                'work_date' => $today,
            ],
            [
                'status'     => $validated['status'],
                'clock_in'   => $validated['clock_in'] ?? now()->format('H:i'),
                'clock_out'  => $validated['clock_out'] ?? null,
                'note'       => $validated['note'] ?? null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'Pointage enregistré pour la journée.');
    }

    /**
     * Accorder une avance sur salaire (et générer automatiquement la sortie de caisse dans expenses)
     */
    public function storeAdvance(Request $request)
    {
        $validated = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'amount'         => 'required|numeric|min:500',
            'advance_date'   => 'required|date',
            'payment_method' => 'required|string|max:50',
            'reason'         => 'nullable|string|max:255',
        ]);

        $employee = User::findOrFail($validated['user_id']);

        DB::transaction(function () use ($validated, $employee) {
            // 1. Enregistrement de l'avance RH
            DB::table('staff_advances')->insert([
                'user_id'        => $employee->id,
                'amount'         => $validated['amount'],
                'advance_date'   => $validated['advance_date'],
                'payment_method' => $validated['payment_method'],
                'reason'         => $validated['reason'] ?? 'Acompte sur salaire',
                'approved_by'    => Auth::id(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // 2. Création automatique du bon de sortie dans Dépenses & Petite Caisse
            $datePrefix = Carbon::parse($validated['advance_date'])->format('Ymd');
            $countToday = DB::table('expenses')->whereDate('expense_date', $validated['advance_date'])->count() + 1;
            $voucherNumber = 'DEP-' . $datePrefix . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

            DB::table('expenses')->insert([
                'voucher_number' => $voucherNumber,
                'category'       => 'autre',
                'description'    => "Avance sur salaire : {$employee->name}" . ($validated['reason'] ? " ({$validated['reason']})" : ''),
                'amount'         => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'beneficiary'    => $employee->name,
                'reference'      => 'RH-AVANCE',
                'user_id'        => Auth::id(),
                'expense_date'   => $validated['advance_date'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        });

        return back()->with('success', "Acompte de " . number_format($validated['amount'], 0, ',', ' ') . " F enregistré pour {$employee->name} et déduit de la caisse.");
    }
}
