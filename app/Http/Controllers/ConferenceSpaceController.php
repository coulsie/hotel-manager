<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConferenceSpaceController extends Controller
{
    /**
     * Afficher la liste des salles de conférence et les réservations.
     */
       public function index()
    {
        $spaces = DB::table('conference_spaces')
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $bookings = DB::table('space_bookings')
            ->join('conference_spaces', 'space_bookings.space_id', '=', 'conference_spaces.id')
            ->leftJoin('companies', 'space_bookings.company_id', '=', 'companies.id')
            ->leftJoin('rooms', 'space_bookings.room_id', '=', 'rooms.id')
            ->select(
                'space_bookings.*',
                'conference_spaces.name as space_name',
                'conference_spaces.category as space_category',
                'companies.name as company_name',
                'rooms.number as room_number'
            )
            ->orderByDesc('space_bookings.booking_date')
            ->orderBy('space_bookings.start_time')
            ->get();

        $occupiedRooms = \App\Models\Room::where('status', 'occupied')
            ->orderBy('number')
            ->get();

        $stats = [
            'spaces_count'   => $spaces->count(),
            'bookings_today' => $bookings
                ->where('booking_date', \Carbon\Carbon::today()->toDateString())
                ->where('status', '!=', 'cancelled')
                ->count(),
            'revenue_month'  => \App\Models\Billing::whereIn('service_type', ['seminaire', 'coworking'])
                ->where('status', 'completed')
                ->whereMonth('created_at', \Carbon\Carbon::now()->month)
                ->whereYear('created_at', \Carbon\Carbon::now()->year)
                ->sum('amount_paid'),
        ];

        return view('conferences.index', compact('spaces', 'bookings', 'occupiedRooms', 'stats'));
    }

    /**
     * Afficher le formulaire de création d'un espace.
     */
    public function create()
    {
        return view('conferences.create');
    }

    /**
     * Enregistrer un nouvel espace (réunion, séminaire ou coworking).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:reunion,seminaire,coworking',
            'capacity' => 'required|integer|min:1',
            'hourly_rate' => 'nullable|numeric|min:0',
            'half_day_rate' => 'nullable|numeric|min:0',
            'full_day_rate' => 'nullable|numeric|min:0',
            'equipments' => 'nullable|string',
        ]);

        DB::table('conference_spaces')->insert([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'capacity' => $validated['capacity'],
            'hourly_rate' => $validated['hourly_rate'] ?? 0,
            'half_day_rate' => $validated['half_day_rate'] ?? 0,
            'full_day_rate' => $validated['full_day_rate'] ?? 0,
            'equipments' => $validated['equipments'] ?? null,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('conferences.index')->with('success', 'Espace ajouté avec succès.');
    }

    /**
     * Afficher les détails et le planning d'un espace.
     */
    public function show(string $id)
    {
        $space = DB::table('conference_spaces')->where('id', $id)->firstOrFail();

        $bookings = DB::table('space_bookings')
            ->where('space_id', $id)
            ->orderByDesc('booking_date')
            ->orderBy('start_time')
            ->get();

        return view('conferences.show', compact('space', 'bookings'));
    }

    /**
     * Afficher le formulaire de modification d'un espace.
     */
    public function edit(string $id)
    {
        $space = DB::table('conference_spaces')->where('id', $id)->firstOrFail();

        return view('conferences.edit', compact('space'));
    }

    /**
     * Mettre à jour un espace existant.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:reunion,seminaire,coworking',
            'capacity' => 'required|integer|min:1',
            'hourly_rate' => 'nullable|numeric|min:0',
            'half_day_rate' => 'nullable|numeric|min:0',
            'full_day_rate' => 'nullable|numeric|min:0',
            'equipments' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        DB::table('conference_spaces')->where('id', $id)->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'capacity' => $validated['capacity'],
            'hourly_rate' => $validated['hourly_rate'] ?? 0,
            'half_day_rate' => $validated['half_day_rate'] ?? 0,
            'full_day_rate' => $validated['full_day_rate'] ?? 0,
            'equipments' => $validated['equipments'] ?? null,
            'is_active' => $request->has('is_active') ? (int) $request->is_active : 1,
            'updated_at' => now(),
        ]);

        return redirect()->route('conferences.index')->with('success', 'Espace mis à jour.');
    }

    /**
     * Désactiver un espace.
     */
    public function destroy(string $id)
    {
        DB::table('conference_spaces')->where('id', $id)->update([
            'is_active' => 0,
            'updated_at' => now(),
        ]);

        return redirect()->route('conferences.index')->with('success', 'Espace désactivé.');
    }

    /**
     * Enregistrer une réservation dans space_bookings.
     */
       public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'space_id'       => 'required|exists:conference_spaces,id',
            'client_type'    => 'nullable|in:external,room',
            'company_id'     => 'nullable|exists:companies,id',
            'room_id'        => 'required_if:client_type,room|nullable|exists:rooms,id',
            'client_name'    => 'required_unless:client_type,room|nullable|string|max:255',
            'client_phone'   => 'nullable|string|max:255',
            'booking_date'   => 'required|date',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i|after:start_time',
            'pricing_type'   => 'required|in:hourly,half_day,full_day',
            'attendees'      => 'required|integer|min:1',
            'total_amount'   => 'nullable|numeric|min:0',
            'amount_paid'    => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'notes'          => 'nullable|string',
        ], [
            'end_time.after'          => "L'heure de fin doit être postérieure à l'heure de début.",
            'room_id.required_if'     => "Veuillez sélectionner une chambre occupée.",
            'client_name.required_unless' => "Veuillez saisir le nom du client ou de la société.",
        ]);

        $space = DB::table('conference_spaces')->where('id', $validated['space_id'])->first();

        // 1. Vérification de disponibilité et de capacité sur le créneau horaire
        $overlappingBookings = DB::table('space_bookings')
            ->where('space_id', $space->id)
            ->where('booking_date', $validated['booking_date'])
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->get();

        if ($space->category === 'coworking') {
            $usedSeats      = $overlappingBookings->sum('attendees');
            $remainingSeats = max(0, (int) $space->capacity - (int) $usedSeats);
            if ((int) $validated['attendees'] > $remainingSeats) {
                return back()->withInput()->with('error', "Capacité dépassée : il ne reste que {$remainingSeats} poste(s) libre(s) sur ce créneau.");
            }
        } else {
            if ($overlappingBookings->count() > 0) {
                return back()->withInput()->with('error', "Conflit horaire : la salle « {$space->name} » est déjà réservée sur ce créneau.");
            }
            if ((int) $validated['attendees'] > (int) $space->capacity) {
                return back()->withInput()->with('error', "Capacité maximale dépassée ({$space->capacity} personnes max pour cette salle).");
            }
        }

        // 2. Calcul sécurisé du total (prend total_amount du formulaire ou le recalcule côté serveur)
        $start = \Carbon\Carbon::parse($validated['booking_date'] . ' ' . $validated['start_time']);
        $end   = \Carbon\Carbon::parse($validated['booking_date'] . ' ' . $validated['end_time']);
        $hours = max(1, (int) ceil($start->diffInMinutes($end) / 60));

        $unitRate = match ($validated['pricing_type']) {
            'hourly'   => (float) $space->hourly_rate * $hours,
            'half_day' => (float) $space->half_day_rate,
            'full_day' => (float) $space->full_day_rate,
        };
        $computedTotal = ($space->category === 'coworking')
            ? $unitRate * (int) $validated['attendees']
            : $unitRate;

        $total  = isset($validated['total_amount']) && (float) $validated['total_amount'] > 0
            ? (float) $validated['total_amount']
            : $computedTotal;

        $isRoom = ($request->input('client_type') === 'room' && !empty($validated['room_id']));
        $paid   = $isRoom ? 0 : (float) ($validated['amount_paid'] ?? 0);

        if ($paid > $total) {
            return back()->withInput()->with('error', "Le montant encaissé ne peut pas dépasser le montant total (" . number_format($total, 0, ',', ' ') . " F CFA).");
        }

        // 3. Nom affiché du client
        $clientName = trim((string) ($validated['client_name'] ?? ''));
        if ($isRoom) {
            $room = \App\Models\Room::find($validated['room_id']);
            $clientName = "Client Chambre " . ($room->number ?? $validated['room_id']) . ($clientName !== '' ? " ({$clientName})" : '');
        }

        $paymentStatus = 'unpaid';
        if ($paid >= $total && $total > 0) {
            $paymentStatus = 'paid';
        } elseif ($paid > 0) {
            $paymentStatus = 'partial';
        }

        $billing = null;

        DB::transaction(function () use ($validated, $space, $isRoom, $clientName, $total, $paid, $paymentStatus, &$billing) {
            DB::table('space_bookings')->insert([
                'space_id'       => $validated['space_id'],
                'company_id'     => $validated['company_id'] ?? null,
                'room_id'        => $isRoom ? $validated['room_id'] : null,
                'client_name'    => $clientName,
                'client_phone'   => $validated['client_phone'] ?? null,
                'booking_date'   => $validated['booking_date'],
                'start_time'     => $validated['start_time'],
                'end_time'       => $validated['end_time'],
                'pricing_type'   => $validated['pricing_type'],
                'attendees'      => $validated['attendees'],
                'total_amount'   => $total,
                'amount_paid'    => $paid,
                'payment_status' => $paymentStatus,
                'status'         => 'confirmed',
                'notes'          => $validated['notes'] ?? null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            if ($paid > 0) {
                $serviceType = ($space->category === 'coworking') ? 'coworking' : 'seminaire';
                $billing = \App\Models\Billing::create([
                    'receipt_number' => \App\Models\Billing::generateReceiptNumber(),
                    'room_id'        => $isRoom ? $validated['room_id'] : null,
                    'service_type'   => $serviceType,
                    'amount_paid'    => $paid,
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                    'status'         => 'completed',
                ]);
            }
        });

        if ($billing) {
            return redirect()->route('conferences.index')
                ->with('success', "Réservation enregistrée et règlement encaissé (Reçu n° {$billing->receipt_number}).")
                ->with('print_billing_id', $billing->id);
        }

        return redirect()->route('conferences.index')
            ->with('success', $isRoom
                ? "Réservation confirmée et imputée sur la note de {$clientName}."
                : "Réservation enregistrée avec succès.");
    }

    /**
     * Mettre à jour le statut d'une réservation ou encaisser un règlement.
     */
       public function updateBookingStatus(Request $request, string $bookingId)
    {
        $booking = DB::table('space_bookings')
            ->join('conference_spaces', 'space_bookings.space_id', '=', 'conference_spaces.id')
            ->select(
                'space_bookings.*',
                'conference_spaces.name as space_name',
                'conference_spaces.category as space_category'
            )
            ->where('space_bookings.id', $bookingId)
            ->first();

        abort_if(!$booking, 404);

        // 1. Cas d'encaissement / solde direct (Bouton "Encaisser Cash" ou "Solder")
        if ($request->input('action') === 'pay') {
            $remaining = max(0, (float) $booking->total_amount - (float) $booking->amount_paid);

            if ($remaining <= 0) {
                return back()->with('error', "Cette réservation est déjà entièrement soldée.");
            }

            $billing = null;

            DB::transaction(function () use ($request, $booking, $remaining, &$billing) {
                $serviceType = ($booking->space_category === 'coworking') ? 'coworking' : 'seminaire';

                $billing = \App\Models\Billing::create([
                    'receipt_number' => \App\Models\Billing::generateReceiptNumber(),
                    'room_id'        => $booking->room_id,
                    'service_type'   => $serviceType,
                    'amount_paid'    => $remaining,
                    'payment_method' => $request->input('payment_method', 'cash'),
                    'status'         => 'completed',
                ]);

                DB::table('space_bookings')
                    ->where('id', $booking->id)
                    ->update([
                        'amount_paid'    => $booking->total_amount,
                        'payment_status' => 'paid',
                        'updated_at'     => now(),
                    ]);
            });

            return back()
                ->with('success', "Règlement de " . number_format($remaining, 0, ',', ' ') . " F CFA encaissé pour « {$booking->space_name} » (Reçu n° {$billing->receipt_number}).")
                ->with('print_billing_id', $billing->id);
        }

        // 2. Cas d'annulation ou changement de statut classique
        $validated = $request->validate([
            'status'      => 'required|in:confirmed,in_progress,completed,cancelled',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

        $paid  = isset($validated['amount_paid']) ? (float) $validated['amount_paid'] : (float) $booking->amount_paid;
        $total = (float) $booking->total_amount;

        $paymentStatus = 'unpaid';
        if ($paid >= $total && $total > 0) {
            $paymentStatus = 'paid';
        } elseif ($paid > 0) {
            $paymentStatus = 'partial';
        }

        DB::table('space_bookings')->where('id', $bookingId)->update([
            'status'         => $validated['status'],
            'amount_paid'    => $paid,
            'payment_status' => $paymentStatus,
            'updated_at'     => now(),
        ]);

        return back()->with('success', 'Statut de la réservation mis à jour.');
    }

}
