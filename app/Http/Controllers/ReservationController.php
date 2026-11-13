<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Customer;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\Billing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


use Carbon\Carbon;


class ReservationController extends Controller
{
    /**
     * Affiche le planning des réservations futures, le formulaire de création et le fichier clients.
     */
    public function index(Request $request)
    {
        $reservations = Reservation::with(['customer', 'room'])
            ->latest('check_in_date')
            ->paginate(15);

        $rooms = Room::orderBy('number')->get();
        $customers = Customer::orderBy('full_name')->get();

        // Statistiques rapides
        $stats = [
            'arrivals_today' => Reservation::where('status', 'confirmed')
                ->whereDate('check_in_date', today())
                ->count(),
            'upcoming_count' => Reservation::where('status', 'confirmed')
                ->whereDate('check_in_date', '>=', today())
                ->count(),
            'deposits_total' => Reservation::where('status', 'confirmed')
                ->sum('deposit_paid'),
        ];

        return view('reservations.index', compact('reservations', 'rooms', 'customers', 'stats'));
    }



    /**
     * Supprime définitivement une réservation.
     */
    public function destroy(Reservation $reservation)
    {
        if ($reservation->status === 'checked_in') {
            return back()->withErrors(['delete' => 'Impossible de supprimer une réservation dont le client est déjà en séjour.']);
        }

        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'La réservation a été supprimée.');
    }



    /**
     * Convertit une réservation confirmée en arrivée effective (Check-in).
     */
    public function convertToCheckin(Reservation $reservation)
    {
        if ($reservation->room->status === 'occupied') {
            return back()->withErrors(['checkin' => "La chambre {$reservation->room->number} est encore occupée. Veuillez libérer la chambre avant d'effectuer le check-in."]);
        }

        $reservation->update(['status' => 'checked_in']);
        $reservation->room->update(['status' => 'occupied']);

        return redirect()->route('reservations.index')
            ->with('success', "Check-in validé pour {$reservation->customer->full_name} dans la chambre {$reservation->room->number}.");
    }

    /**
     * Annule une réservation future.
     */
    public function cancel(Reservation $reservation)
    {
        $reservation->update(['status' => 'cancelled']);

        return redirect()->route('reservations.index')
            ->with('success', 'La réservation a été annulée.');
    }

    /**
     * Affiche la fiche de police imprimable du client.
     */
    public function policeForm(Reservation $reservation)
    {
        $reservation->load(['customer', 'room']);
        return view('reservations.police_form', compact('reservation'));
    }


    /**
     * Enregistre une nouvelle réservation future avec double contrôle de dates et disponibilité.
     */
       public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'      => 'nullable|exists:customers,id',
            'full_name'        => 'required_without:customer_id|nullable|string|max:191',
            'phone'            => 'required_without:customer_id|nullable|string|max:191',
            'nationality'      => 'nullable|string|max:100',
            'id_type'          => 'nullable|string|max:50',
            'id_card_number'   => 'nullable|string|max:191',
            'address'          => 'nullable|string|max:255',
            'room_id'          => 'required|exists:rooms,id',
            'check_in_date'    => 'required|date|after_or_equal:today',
            'check_out_date'   => 'required|date|after:check_in_date',
            'deposit_paid'     => 'nullable|numeric|min:0',
            'payment_method'   => 'nullable|string|max:50',
            'provenance'       => 'nullable|string|max:191',
            'destination'      => 'nullable|string|max:191',
            'special_requests' => 'nullable|string',
        ], [
            'check_out_date.after' => 'La date de départ doit être strictement supérieure à la date d\'arrivée (minimum 1 nuit).',
        ]);

        // 1. Vérification complète de disponibilité sur les dates précises
        $availabilityError = $this->checkRoomAvailability(
            $validated['room_id'],
            $validated['check_in_date'],
            $validated['check_out_date']
        );

        if ($availabilityError) {
            return back()->withErrors(['room_id' => $availabilityError])->withInput();
        }

        // 2. Récupération ou création de la fiche client
        if (!empty($validated['customer_id'])) {
            $customer = Customer::findOrFail($validated['customer_id']);
        } else {
            $customer = Customer::updateOrCreate(
                ['phone' => $validated['phone']],
                [
                    'full_name'      => $validated['full_name'],
                    'nationality'    => $validated['nationality'] ?? 'Ivoirienne',
                    'id_type'        => $validated['id_type'] ?? 'CNI',
                    'id_card_number' => $validated['id_card_number'] ?? null,
                    'address'        => $validated['address'] ?? null,
                ]
            );
        }

        // 3. Calcul des nuitées et du total
        $room = Room::findOrFail($validated['room_id']);
        $nights = Carbon::parse($validated['check_in_date'])->diffInDays(Carbon::parse($validated['check_out_date']));
        $totalAmount = $nights * $room->price_per_night;
        $depositPaid = $validated['deposit_paid'] ?? 0;

        $billing = null;

        // 4. Création de la réservation et enregistrement des arrhes dans le Grand Livre
        DB::transaction(function () use ($validated, $customer, $room, $nights, $totalAmount, $depositPaid, &$billing) {
            Reservation::create([
                'customer_id'      => $customer->id,
                'room_id'          => $room->id,
                'check_in_date'    => $validated['check_in_date'],
                'check_out_date'   => $validated['check_out_date'],
                'nights'           => $nights,
                'total_amount'     => $totalAmount,
                'deposit_paid'     => $depositPaid,
                'status'           => 'confirmed',
                'provenance'       => $validated['provenance'] ?? null,
                'destination'      => $validated['destination'] ?? null,
                'special_requests' => $validated['special_requests'] ?? null,
            ]);

            if ($depositPaid > 0) {
                $billing = Billing::create([
                    'receipt_number' => Billing::generateReceiptNumber(),
                    'room_id'        => $room->id,
                    'service_type'   => 'reservation_deposit',
                    'amount_paid'    => $depositPaid,
                    'payment_method' => $validated['payment_method'] ?? 'Espèces',
                    'status'         => 'completed',
                ]);
            }
        });

        // 5. Si un acompte a été encaissé, redirection vers le Grand Livre avec impression du reçu
        if ($billing) {
            return redirect()->route('billing.payments')
                ->with('success', "Réservation confirmée pour {$customer->full_name} et arrhes encaissées (Reçu n° {$billing->receipt_number}).")
                ->with('print_billing_id', $billing->id);
        }

        return redirect()->route('reservations.index')
            ->with('success', "Réservation confirmée pour {$customer->full_name} (Chambre {$room->number} - {$nights} nuit(s)).");
    }


    /**
     * Met à jour une réservation existante avec contrôle des dates et des conflits.
     */
    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'room_id'        => 'required|exists:rooms,id',
            'check_in_date'  => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'deposit_paid'   => 'nullable|numeric|min:0',
        ], [
            'check_out_date.after' => 'La date de départ doit être strictement supérieure à la date d\'arrivée.',
        ]);

        // Vérification de disponibilité en excluant cette réservation
        $availabilityError = $this->checkRoomAvailability(
            $validated['room_id'],
            $validated['check_in_date'],
            $validated['check_out_date'],
            $reservation->id
        );

        if ($availabilityError) {
            return back()->withErrors(['room_id' => $availabilityError]);
        }

        $room = Room::findOrFail($validated['room_id']);
        $nights = Carbon::parse($validated['check_in_date'])->diffInDays(Carbon::parse($validated['check_out_date']));
        $totalAmount = $nights * $room->price_per_night;

        $reservation->update([
            'room_id'        => $room->id,
            'check_in_date'  => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'nights'         => $nights,
            'total_amount'   => $totalAmount,
            'deposit_paid'   => $validated['deposit_paid'] ?? 0,
        ]);

        return redirect()->route('reservations.index')
            ->with('success', 'La réservation a été mise à jour avec succès.');
    }

    /**
     * Vérifie si la chambre demandée est libre et propose des alternatives ou signale l'hôtel complet.
     */
       /**
     * Vérifie si la chambre demandée est libre et propose des alternatives ou signale l'hôtel complet.
     */
    private function checkRoomAvailability(int $roomId, string $checkIn, string $checkOut, ?int $ignoreReservationId = null): ?string
    {
        $room = Room::findOrFail($roomId);
        $inFormatted = Carbon::parse($checkIn)->format('d/m/Y');
        $outFormatted = Carbon::parse($checkOut)->format('d/m/Y');

        // 1. Vérifier si la chambre est en maintenance ou déjà occupée aujourd'hui pour une arrivée immédiate
        $isUnavailable = ($room->status === 'maintenance')
            || ($room->status === 'occupied' && Carbon::parse($checkIn)->isToday());

        // 2. Vérifier les chevauchements dans la table 'reservations'
        if (!$isUnavailable) {
            $isUnavailable = Reservation::where('room_id', $roomId)
                ->where('id', '!=', $ignoreReservationId)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->where('check_in_date', '<', $checkOut)
                ->where('check_out_date', '>', $checkIn)
                ->exists();
        }

        // Si la chambre est libre, aucune erreur
        if (!$isUnavailable) {
            return null;
        }

        // 3. La chambre est indisponible : chercher toutes les autres chambres libres sur ce créneau
        $busyRoomIds = Reservation::where('id', '!=', $ignoreReservationId)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->pluck('room_id')
            ->toArray();

        $freeRoomsQuery = Room::where('status', '!=', 'maintenance')
            ->whereNotIn('id', $busyRoomIds);

        // Exclure également les chambres actuellement occupées si l'arrivée est aujourd'hui
        if (Carbon::parse($checkIn)->isToday()) {
            $freeRoomsQuery->where('status', '!=', 'occupied');
        }

        $freeRooms = $freeRoomsQuery->orderBy('number')->get();

        // Cas A : Aucune chambre disponible dans tout l'hôtel
        if ($freeRooms->isEmpty()) {
            return "INDISPONIBILITÉ HÔTEL : La chambre {$room->number} est occupée et aucune autre chambre n'est disponible dans l'hôtel du {$inFormatted} au {$outFormatted}.";
        }

        // Cas B : D'autres chambres sont libres, on affiche la liste pour changer immédiatement
        $suggestions = $freeRooms->map(fn($r) => "Ch. {$r->number} ({$r->type})")->implode(', ');
        return "La chambre {$room->number} n'est pas disponible du {$inFormatted} au {$outFormatted}. Veuillez changer de chambre. Chambres libres sur ces dates : {$suggestions}.";
    }



public function checkin(Reservation $reservation)
{
    if ($reservation->status !== 'confirmed') {
        return back()->with('error', 'Cette réservation a déjà été traitée ou annulée.');
    }

    $room = $reservation->room;

    if ($room->status !== 'available') {
        return back()->with('error', "Impossible d'effectuer le check-in : la Chambre {$room->number} est actuellement indisponible.");
    }

    // Calcul exact des nuitées (ex: 7 nuits) et de la date de départ (ex: 20/11/2026)
    $checkInDate  = Carbon::parse($reservation->check_in_date)->startOfDay();
    $checkOutDate = Carbon::parse($reservation->check_out_date)->startOfDay();
    $nights       = max(1, $checkInDate->diffInDays($checkOutDate));
    $totalAmount  = $nights * $room->price_per_night;
    $resteAPayer  = max(0, $totalAmount - $reservation->deposit_paid);

    DB::transaction(function () use ($reservation, $room, $nights, $totalAmount, $checkOutDate) {
        // 1. Désactiver toute ancienne carte active sur cette chambre
        DB::table('magnetic_cards')
            ->where('room_id', $room->id)
            ->where('is_active', 1)
            ->update([
                'is_active'  => 0,
                'updated_at' => now(),
            ]);

        // 2. Créer la carte magnétique pour ce séjour (indispensable pour BillingController)
        DB::table('magnetic_cards')->insert([
            'card_uid'       => 'RFID_' . strtoupper(Str::random(6)),
            'security_token' => Str::random(32),
            'room_id'        => $room->id,
            'duration_days'  => $nights,
            'expires_at'     => $checkOutDate->copy()->setTime(12, 0, 0),
            'is_active'      => 1,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // 3. Passer la chambre en "occupied"
        $room->update(['status' => 'occupied']);

        // 4. Mettre à jour la réservation en "checked_in" (sans recréer de paiement dans Billing)
        $reservation->update([
            'nights'       => $nights,
            'total_amount' => $totalAmount,
            'status'       => 'checked_in',
        ]);
    });

    $acompteFormate = number_format($reservation->deposit_paid, 0, ',', ' ');
    $resteFormate   = number_format($resteAPayer, 0, ',', ' ');

    return redirect()->route('billing.index')
        ->with('success', "Check-in validé pour {$reservation->customer->full_name} (Ch. {$room->number} - {$nights} nuits). Acompte déjà encaissé : {$acompteFormate} F. Reste à payer : {$resteFormate} F.");
}

public function rack(Request $request)
{
    // 1. Déterminer le mois affiché (par défaut le mois en cours, ex: 2026-11)
    $monthParam = $request->input('month', now()->format('Y-m'));
    try {
        $startOfMonth = Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
    } catch (\Exception $e) {
        $startOfMonth = now()->startOfMonth();
    }

    $endOfMonth = $startOfMonth->copy()->endOfMonth();
    $prevMonth  = $startOfMonth->copy()->subMonth()->format('Y-m');
    $nextMonth  = $startOfMonth->copy()->addMonth()->format('Y-m');

    // 2. Générer la liste de tous les jours du mois
    $days = [];
    for ($d = 1; $d <= $startOfMonth->daysInMonth; $d++) {
        $days[] = $startOfMonth->copy()->day($d);
    }

    // 3. Charger toutes les chambres triées par numéro
    $rooms = Room::orderBy('number')->get();

    // 4a. Charger les réservations (confirmées ou en cours) qui chevauchent ce mois
    $reservations = Reservation::with('customer')
        ->whereIn('status', ['confirmed', 'checked_in'])
        ->whereDate('check_in_date', '<=', $endOfMonth->toDateString())
        ->whereDate('check_out_date', '>', $startOfMonth->toDateString())
        ->get();

    // 4b. Charger les séjours directs actifs (cartes magnétiques actives sans réservation liée)
    $activeCards = DB::table('magnetic_cards')
        ->where('is_active', 1)
        ->get();

    // 5. Construire la matrice du Rack (Chambre x Jour)
    $rackMatrix = [];

    foreach ($rooms as $room) {
        $row = [];

        // Chercher une éventuelle carte active pour cette chambre (cas d'un check-in direct sans réservation)
        $roomCard = $activeCards->firstWhere('room_id', $room->id);
        $cardIn   = $roomCard ? Carbon::parse($roomCard->created_at)->toDateString() : null;
        $cardOut  = ($roomCard && $roomCard->expires_at)
            ? Carbon::parse($roomCard->expires_at)->toDateString()
            : ($roomCard ? Carbon::parse($roomCard->created_at)->addDays($roomCard->duration_days ?? 1)->toDateString() : null);

        foreach ($days as $day) {
            $dateStr = $day->toDateString();

            // Priorité 1 : Vérifier dans la table des réservations (confirmed ou checked_in)
            $booking = $reservations->first(function ($res) use ($room, $dateStr) {
                $in  = Carbon::parse($res->check_in_date)->toDateString();
                $out = Carbon::parse($res->check_out_date)->toDateString();
                return $res->room_id == $room->id && $dateStr >= $in && $dateStr < $out;
            });

            if ($booking) {
                $isFirstDay = (Carbon::parse($booking->check_in_date)->toDateString() === $dateStr);
                $row[$dateStr] = [
                    'state'     => $booking->status, // 'checked_in' (vert) ou 'confirmed' (orange)
                    'client'    => $booking->customer->full_name ?? 'Client',
                    'dates_txt' => 'Du ' . Carbon::parse($booking->check_in_date)->format('d/m') . ' au ' . Carbon::parse($booking->check_out_date)->format('d/m'),
                    'is_start'  => $isFirstDay,
                ];
            }
            // Priorité 2 : Vérifier si la chambre est occupée via un check-in direct (carte RFID active)
            elseif ($room->status === 'occupied' && $roomCard && $dateStr >= $cardIn && $dateStr < $cardOut) {
                $isFirstDay = ($cardIn === $dateStr);
                $row[$dateStr] = [
                    'state'     => 'checked_in', // Affiché en vert comme séjour en cours
                    'client'    => 'Client sur place',
                    'dates_txt' => 'Du ' . Carbon::parse($cardIn)->format('d/m') . ' au ' . Carbon::parse($cardOut)->format('d/m'),
                    'is_start'  => $isFirstDay,
                ];
            }
            // Priorité 3 : Chambre en maintenance ou libre
            else {
                $row[$dateStr] = [
                    'state' => ($room->status === 'maintenance') ? 'maintenance' : 'free',
                ];
            }
        }

        $rackMatrix[] = [
            'room'  => $room,
            'cells' => $row,
        ];
    }

    return view('reservations.rack', compact(
        'startOfMonth',
        'prevMonth',
        'nextMonth',
        'days',
        'rackMatrix'
    ));
}




}
