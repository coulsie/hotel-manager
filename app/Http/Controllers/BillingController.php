<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Order;
use App\Models\Room;
use App\Models\EventTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use Carbon\Carbon;

class BillingController extends Controller
{
       /**
     * PAGE 1 : Encours Clients & Ventes en Attente (billing.index)
     */
        /**
     * PAGE 1 : Encours Clients & Factures en Attente (Hôtel + Restaurant)
     * URL : http://127.0.0.1:8000/billing
     */
    /**
     * PAGE 1 : Encours Clients & Factures en Attente (billing.index)
     * URL : http://127.0.0
     */
        /**
     * PAGE 1 : Encours Clients & Factures en Attente (billing.index)
     * Basé sur la colonne simplifiée duration_days
     */
    public function index()
    {
        // 1. Récupération des commandes restaurant non soldées (comptoir + chambres)
        $pendingOrders = Order::with(['restaurantTable', 'room'])
            ->where(function ($query) {
                $query->whereNotIn('status', ['paid', 'completed'])
                      ->orWhereNull('status');
            })
            ->latest()
            ->get();

        // 1 bis. Récupération des prestations Pressing & Lavage Auto non soldées
        $pendingExtraServices = \App\Models\ExtraService::with('room')
            ->where('payment_status', 'unpaid')
            ->latest()
            ->get();

        // 2. Suivi complet de TOUTES les chambres actuellement occupées
        $occupiedRooms = Room::where('status', 'occupied')->get();
        $pendingRooms = [];

        foreach ($occupiedRooms as $room) {
            $activeCard = DB::table('magnetic_cards')
                ->where('room_id', $room->id)
                ->where('is_active', 1)
                ->latest('id')
                ->first();

            $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;
            $stayStartDate = $activeCard ? Carbon::parse($activeCard->created_at) : now()->startOfDay();
            $checkoutDate  = ($activeCard && $activeCard->expires_at)
                ? Carbon::parse($activeCard->expires_at)
                : $stayStartDate->copy()->addDays($days)->setTime(12, 0, 0);

            $roomCost = $room->price_per_night * $days;

            $restoCost = Order::where('room_id', $room->id)
                ->where(function ($q) {
                    $q->whereNotIn('status', ['paid', 'completed'])
                      ->orWhereNull('status');
                })
                ->sum('total_amount');

            $pressingAutoCost = \App\Models\ExtraService::where('room_id', $room->id)
                ->where('payment_status', 'unpaid')
                ->sum('total_amount');

            $activitiesCost = DB::table('activity_bookings')
                ->where('room_id', $room->id)
                ->where('payment_status', 'unpaid')
                ->sum('total_amount');

            $extrasCost = $restoCost + $pressingAutoCost + $activitiesCost;
            $totalCost  = $roomCost + $extrasCost;

            // Réservation éventuelle convertie en check-in
            $checkedInReservation = Reservation::where('room_id', $room->id)
                ->where('status', 'checked_in')
                ->latest('id')
                ->first();

            $reservationDeposit = $checkedInReservation ? (float) $checkedInReservation->deposit_paid : 0;

            // Paiements encaissés APRÈS la création de la réservation (évite le double comptage de l'acompte)
            $cutoffDate = $checkedInReservation
                ? Carbon::parse($checkedInReservation->created_at)->addSeconds(5)
                : $stayStartDate->copy()->subSeconds(5);

            $stayPayments = Billing::where('room_id', $room->id)
                ->where('service_type', 'room_booking')
                ->where('status', 'completed')
                ->where('created_at', '>', $cutoffDate)
                ->sum('amount_paid');

            $amountPaid     = $reservationDeposit + $stayPayments;
            $remainingToPay = max(0, $totalCost - $amountPaid);

            $pendingRooms[] = [
                'id'                  => $room->id,
                'room_number'         => $room->number,
                'room_type'           => $room->type,
                'created_at'          => $stayStartDate,
                'checkout_date'       => $checkoutDate,
                'days'                => $days,
                'room_cost'           => $roomCost,
                'extras_cost'         => $extrasCost,
                'total_cost'          => $totalCost,
                'reservation_deposit' => $reservationDeposit,
                'amount_paid'         => $amountPaid,
                'remaining'           => $remainingToPay,
            ];
        }

        return view('billing.index', compact('pendingOrders', 'pendingExtraServices', 'pendingRooms'));
    }



        /**
         * PAGE 2 : Grand Livre des Recettes (billing.payments)
         */

        /**
         * PAGE 2 : Grand Livre des Recettes (billing.payments)
         * URL : http://127.0.0/payments
         */
        /**
         * PAGE 2 : Grand Livre des Recettes (billing.payments)
         * URL : http://127.0.0
         */
 public function paymentsHistory(\Illuminate\Http\Request $request)
{
    // 1. Initialisation de la requête de base avec chargement des relations
    $query = \App\Models\Billing::with(['room', 'order.restaurantTable', 'eventTicket.event'])
        ->where('status', 'completed');

    // 2. Filtrage Temporel (Journalier, Hebdomadaire, Mensuel)
    if ($request->filled('period')) {
        switch ($request->input('period')) {
            case 'daily':
                $query->whereDate('created_at', \Carbon\Carbon::today());
                break;
            case 'weekly':
                $query->whereBetween('created_at', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('created_at', \Carbon\Carbon::now()->month)
                      ->whereYear('created_at', \Carbon\Carbon::now()->year);
                break;
        }
    }

    // 3. Moteur de Recherche multicritère (N° Reçu, Mode, Numéro de Chambre)
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('receipt_number', 'LIKE', "%{$search}%")
              ->orWhere('payment_method', 'LIKE', "%{$search}%")
              ->orWhereHas('room', function ($r) use ($search) {
                  $r->where('number', 'LIKE', "%{$search}%");
              });
        });
    }

    // 4. Récupération des transactions filtrées et calcul du total global (inclut bien les arrhes !)
    $allBillings = $query->latest()->get();
    $periodTotal = $allBillings->sum('amount_paid');

      // 5. REGROUPEMENT PAR DOSSIER CHAMBRE (Séjours en cours, passés et réservations avec acompte)
    $roomAccounts = [];

    $billingRoomIds = $allBillings->whereNotNull('room_id')->pluck('room_id');
    $checkedInRoomIds = Reservation::where('status', 'checked_in')->pluck('room_id');
    $allRelevantRoomIds = $billingRoomIds->merge($checkedInRoomIds)->unique();

    foreach ($allRelevantRoomIds as $roomId) {
        $room = Room::find($roomId);
        if (!$room) {
            continue;
        }

        $transactions = $allBillings->where('room_id', $roomId);

        // Réservation en cours (checked_in) ou confirmée (confirmed) associée à cette chambre
        $activeOrFutureRes = Reservation::where('room_id', $roomId)
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->latest('id')
            ->first();

        $activeCard = DB::table('magnetic_cards')
            ->where('room_id', $roomId)
            ->where('is_active', 1)
            ->latest('id')
            ->first();

        // Calcul exact des nuits et du coût chambre : Carte active -> Réservation (ex: 2 nuits = 90 000 F) -> 1 nuit
        if ($activeCard && (int) $activeCard->duration_days > 0) {
            $days = (int) $activeCard->duration_days;
            $roomBaseCost = $room->price_per_night * $days;
        } elseif ($activeOrFutureRes) {
            $days = (int) $activeOrFutureRes->nights;
            $roomBaseCost = (float) $activeOrFutureRes->total_amount;
        } else {
            $lastCard = DB::table('magnetic_cards')->where('room_id', $roomId)->latest('id')->first();
            $days = $lastCard ? (int) ($lastCard->duration_days ?? 1) : 1;
            $roomBaseCost = $room->price_per_night * $days;
        }

        $roomOrders = Order::where('room_id', $roomId)->with('restaurantTable')->get();
        $roomExtraServices = \App\Models\ExtraService::where('room_id', $roomId)->get();
        $roomOtherBillings = $transactions->whereNotIn('service_type', ['room_booking', 'reservation_deposit']);

        $extrasTotal = $roomOrders->sum('total_amount')
                     + $roomExtraServices->sum('total_amount')
                     + $roomOtherBillings->sum('amount_paid');

        // Engagement global (Chambre + Extras)
        $totalEngagement = $roomBaseCost + $extrasTotal;

        $reservationDeposit = $activeOrFutureRes ? (float) $activeOrFutureRes->deposit_paid : 0;

        // Total déjà payé (sans double compter l'acompte déjà inclus dans $transactions)
        $totalPaid = $transactions->sum('amount_paid');
        if ($totalPaid < $reservationDeposit) {
            $totalPaid = $reservationDeposit;
        }

        // Reste à payer (0 F uniquement si la chambre est libre ET sans réservation future en attente)
        $remaining = ($room->status === 'available' && !$activeOrFutureRes)
            ? 0
            : max(0, $totalEngagement - $totalPaid);

        $roomAccounts[] = [
            'room'                => $room,
            'days'                => $days,
            'room_cost'           => $roomBaseCost,
            'resto_cost'          => $extrasTotal,
            'orders'              => $roomOrders,
            'extra_services'      => $roomExtraServices,
            'other_billings'      => $roomOtherBillings,
            'total_engagement'    => $totalEngagement,
            'reservation_deposit' => $reservationDeposit,
            'total_paid'          => $totalPaid,
            'remaining'           => $remaining,
            'transactions'        => $transactions,
        ];
    }

    // 5 bis. RUBRIQUE RÉSERVATIONS, ARRIVÉES & SUIVI DES ARRHES
    $reservations = Reservation::with(['customer', 'room'])
        ->where(function ($q) {
            $q->whereIn('status', ['confirmed', 'checked_in'])
              ->orWhere(function ($sub) {
                  $sub->where('status', 'cancelled')->where('deposit_paid', '>', 0);
              });
        })
        ->orderByDesc('check_in_date')
        ->get();

    $reservationAccounts = [];
    foreach ($reservations as $res) {
        $depositBillings = Billing::where('room_id', $res->room_id)
            ->whereIn('service_type', ['room_booking', 'reservation_deposit'])
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::parse($res->created_at)->subSeconds(5))
            ->orderBy('created_at', 'asc')
            ->get();

        $effectiveNights = (int) $res->nights;
        $effectiveTotal  = (float) $res->total_amount;

        if ($res->status === 'checked_in') {
            // Synchronisation avec la carte magnétique active (en cas de prolongation ou réduction de séjour)
            $activeCard = DB::table('magnetic_cards')
                ->where('room_id', $res->room_id)
                ->where('is_active', 1)
                ->latest('id')
                ->first();

            if ($activeCard && (int) $activeCard->duration_days > 0 && $res->room) {
                $effectiveNights = (int) $activeCard->duration_days;
                $effectiveTotal  = $effectiveNights * (float) $res->room->price_per_night;
            }

            // Paiements complémentaires encaissés après la création de la réservation
            $afterBookingPayments = Billing::where('room_id', $res->room_id)
                ->whereIn('service_type', ['room_booking', 'reservation_deposit'])
                ->where('status', 'completed')
                ->where('created_at', '>', Carbon::parse($res->created_at)->addSeconds(5))
                ->sum('amount_paid');

            $totalPaid = (float) $res->deposit_paid + $afterBookingPayments;
        } else {
            $totalPaid = (float) $res->deposit_paid;
        }

        if ($res->status === 'cancelled') {
            $effectiveTotal = (float) $res->deposit_paid;
            $remainingRes   = 0;
        } else {
            $remainingRes = max(0, $effectiveTotal - $totalPaid);
        }

        $reservationAccounts[] = [
            'reservation'      => $res,
            'effective_nights' => $effectiveNights,
            'total_amount'     => $effectiveTotal,
            'deposit_paid'     => $totalPaid,
            'remaining'        => $remainingRes,
            'billings'         => $depositBillings,
        ];
    }

    // 6. VENTES DIRECTES HORS CHAMBRE
    $directTransactions = $allBillings->whereNull('room_id');

    // 7. Envoi vers la vue avec les dossiers groupés + les réservations
    return view('billing.payments', compact(
        'roomAccounts',
        'reservationAccounts',
        'directTransactions',
        'periodTotal'
    ));
}

    /**
     * Valide le paiement d'une commande restaurant et génère le reçu dans le Grand Livre.
     */
    public function storeRestaurantPayment(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->status === 'paid' || $order->status === 'completed') {
            return redirect()->back()->with('error', "Sécurité : Cette note de restauration a déjà été encaissée.");
        }

        $billing = Billing::create([
            'receipt_number' => Billing::generateReceiptNumber(),
            'order_id'       => $order->id,
            'room_id'        => $order->room_id,
            'service_type'   => 'restaurant',
            'amount_paid'    => $order->total_amount,
            'payment_method' => $request->input('payment_method', 'cash'),
            'status'         => 'completed'
        ]);

        $order->update(['status' => 'paid']);

        // Redirige vers l'historique des paiements validés pour voir le reçu immédiatement
        return redirect()->route('billing.payments')->with('success', "Encaissement Restaurant validé. Reçu officiel émis : n° {$billing->receipt_number}");
    }



    /**
     * Affiche le détail de la facture d'une chambre pour le Checkout.
     * Route : billing.room
     */

    public function showRoomInvoice($roomId)
    {
        $room = Room::findOrFail($roomId);

        $activeCard = DB::table('magnetic_cards')
            ->where('room_id', $room->id)
            ->where('is_active', 1)
            ->latest('id')
            ->first();

        $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;
        $stayStartDate = $activeCard ? Carbon::parse($activeCard->created_at) : now()->startOfDay();

        $roomCost = $room->price_per_night * $days;

        $orders = Order::where('room_id', $room->id)
            ->where(function ($q) {
                $q->whereNotIn('status', ['paid', 'completed'])
                  ->orWhereNull('status');
            })
            ->with('restaurantTable')
            ->get();

        $restaurantTotal = $orders->sum('total_amount');

        $extraServices = \App\Models\ExtraService::where('room_id', $room->id)
            ->where('payment_status', 'unpaid')
            ->get();

        $extraServicesTotal = $extraServices->sum('total_amount');
        $showsTotal = 0;

        $totalInvoice = $roomCost + $restaurantTotal + $extraServicesTotal + $showsTotal;

        // Déduction de l'acompte réservation + règlements effectués pendant le séjour
        $checkedInReservation = Reservation::where('room_id', $room->id)
            ->where('status', 'checked_in')
            ->latest('id')
            ->first();

        $reservationDeposit = $checkedInReservation ? (float) $checkedInReservation->deposit_paid : 0;
        $cutoffDate = $checkedInReservation
            ? Carbon::parse($checkedInReservation->created_at)->addSeconds(5)
            : $stayStartDate->copy()->subSeconds(5);

        $stayPayments = Billing::where('room_id', $room->id)
            ->where('service_type', 'room_booking')
            ->where('status', 'completed')
            ->where('created_at', '>', $cutoffDate)
            ->sum('amount_paid');

        $alreadyPaid    = $reservationDeposit + $stayPayments;
        $remainingToPay = max(0, $totalInvoice - $alreadyPaid);
        $isPaid         = ($remainingToPay <= 0);

        return view('billing.room_invoice', compact(
            'room',
            'roomCost',
            'orders',
            'restaurantTotal',
            'extraServices',
            'extraServicesTotal',
            'showsTotal',
            'totalInvoice',
            'alreadyPaid',
            'remainingToPay',
            'days',
            'isPaid'
        ));
    }


    /**
     * Enregistre le paiement final ou un acompte pour l'hébergement
     */
public function storeRoomPayment(Request $request, $roomId)
{
    $room = Room::findOrFail($roomId);

    $request->validate([
        'amount_paid'    => 'required|numeric|min:0',
        'payment_method' => 'required|string'
    ]);

    $billing = DB::transaction(function () use ($request, $room) {
        // 1. Enregistrement du règlement dans le Grand Livre avec numéro de reçu
        $billing = Billing::create([
            'receipt_number' => Billing::generateReceiptNumber(),
            'room_id'        => $room->id,
            'service_type'   => 'room_booking',
            'amount_paid'    => $request->input('amount_paid'),
            'payment_method' => $request->input('payment_method'),
            'status'         => 'completed'
        ]);

        // 2. Clôturer les commandes restaurant non payées de cette chambre
        \App\Models\Order::where('room_id', $room->id)
            ->where('status', '!=', 'paid')
            ->update(['status' => 'paid']);

        // 3. Clôturer les prestations Pressing & Lavage Auto non payées de cette chambre
        \App\Models\ExtraService::where('room_id', $room->id)
            ->where('payment_status', 'unpaid')
            ->update(['payment_status' => 'paid']);

        // 4. Clôturer les nouvelles Activités (Piscine, Spa, Navette, Coworking...) non payées de cette chambre
        DB::table('activity_bookings')
            ->where('room_id', $room->id)
            ->where('payment_status', 'unpaid')
            ->update([
                'payment_status' => 'paid',
                'updated_at'     => now(),
            ]);

        return $billing;
    });

    // 5. Redirection vers le Grand Livre (payments.blade.php) avec impression du reçu
    return redirect()->route('billing.payments')
        ->with('success', "Encaissement Hébergement validé (Reçu n° {$billing->receipt_number}). La chambre reste attribuée jusqu'à la fin du séjour.")
        ->with('print_billing_id', $billing->id);
}



    /**
     * Enregistre la vente d'un ticket de spectacle
     */
    public function storeShowPayment(Request $request, $ticketId)
    {
        $ticket = EventTicket::findOrFail($ticketId);

        $billing = Billing::create([
            'receipt_number' => Billing::generateReceiptNumber(),
            'event_ticket_id'=> $ticket->id,
            'service_type'   => 'show_ticket',
            'amount_paid'    => $ticket->price,
            'payment_method' => $request->input('payment_method', 'cash'),
            'status'         => 'completed'
        ]);

        if ($ticket->available_seats > 0) {
            $ticket->decrement('available_seats');
        }

        return redirect()->route('billing.payments')->with('success', "Billet Spectacle émis. Reçu n° {$billing->receipt_number}");
    }

    /**
     * Action d'impression ou d'ouverture du reçu
     */
    public function printReceipt($id)
    {
        $billing = Billing::with(['room', 'order', 'eventTicket.event'])->findOrFail($id);

        return view('pdf.receipt', compact('billing'));
    }


    public function destroyPayment($id)
    {
        $billing = \App\Models\Billing::findOrFail($id);
        $billing->delete();

        return redirect()->route('billing.payments')
            ->with('success', "La ligne du reçu n° {$billing->receipt_number} a été supprimée du Grand Livre.");
    }


    public function exportPaymentsCsv(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Billing::with(['room', 'order', 'eventTicket.event'])
            ->where('status', 'completed');

        // Applique les mêmes filtres que sur la page Grand Livre
        if ($request->filled('period')) {
            switch ($request->input('period')) {
                case 'daily':
                    $query->whereDate('created_at', \Carbon\Carbon::today());
                    break;
                case 'weekly':
                    $query->whereBetween('created_at', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
                    break;
                case 'monthly':
                    $query->whereMonth('created_at', \Carbon\Carbon::now()->month)
                        ->whereYear('created_at', \Carbon\Carbon::now()->year);
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'LIKE', "%{$search}%")
                ->orWhere('payment_method', 'LIKE', "%{$search}%")
                ->orWhereHas('room', function ($r) use ($search) {
                    $r->where('number', 'LIKE', "%{$search}%");
                });
            });
        }

        $billings = $query->latest()->get();
        $fileName = 'Grand_Livre_Hotel_Manager_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($billings) {
            $file = fopen('php://output', 'w');
            // Ajout du BOM UTF-8 pour une ouverture parfaite dans Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // En-têtes des colonnes
            fputcsv($file, ['N° Reçu', 'Date & Heure', 'Type de Service', 'Chambre / Origine', 'Mode de Règlement', 'Montant Encaissé (F CFA)'], ';');

            foreach ($billings as $b) {
                $serviceLabel = match ($b->service_type) {
                    'room_booking' => 'Hébergement Hôtel',
                    'restaurant'   => 'Restauration & Bar',
                    'show_ticket'  => 'Billetterie Spectacle',
                    default        => $b->service_type,
                };

                $origin = $b->room ? 'Chambre ' . $b->room->number : ($b->order ? 'Commande #' . $b->order->id : 'Comptoir Direct');

                fputcsv($file, [
                    $b->receipt_number,
                    $b->created_at ? $b->created_at->format('d/m/Y H:i') : '',
                    $serviceLabel,
                    $origin,
                    strtoupper($b->payment_method),
                    $b->amount_paid
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

public function dailyCloseReport(\Illuminate\Http\Request $request)
{
    $date = $request->filled('date') ? \Carbon\Carbon::parse($request->input('date')) : \Carbon\Carbon::today();

    $transactions = \App\Models\Billing::with(['room', 'order', 'eventTicket.event'])
        ->where('status', 'completed')
        ->whereDate('created_at', $date)
        ->get();

    // 1. Ventilation par mode de paiement (insensible à la casse et aux variantes)
    $byMethod = [
        'cash'         => 0,
        'wave'         => 0,
        'orange_money' => 0,
        'mtn_momo'     => 0,
        'moov_money'   => 0,
        'card'         => 0,
        'other'        => 0,
    ];

    foreach ($transactions as $tx) {
        $m = mb_strtolower(trim((string) $tx->payment_method));

        if (in_array($m, ['cash', 'espèces', 'especes'])) {
            $byMethod['cash'] += $tx->amount_paid;
        } elseif (str_contains($m, 'wave')) {
            $byMethod['wave'] += $tx->amount_paid;
        } elseif (str_contains($m, 'orange')) {
            $byMethod['orange_money'] += $tx->amount_paid;
        } elseif (str_contains($m, 'mtn')) {
            $byMethod['mtn_momo'] += $tx->amount_paid;
        } elseif (str_contains($m, 'moov')) {
            $byMethod['moov_money'] += $tx->amount_paid;
        } elseif (in_array($m, ['card', 'carte', 'tpe', 'carte bancaire'])) {
            $byMethod['card'] += $tx->amount_paid;
        } else {
            $byMethod['other'] += $tx->amount_paid;
        }
    }

    // 2. Ventilation par pôle d'activité (Hébergement, Resto, Pressing, Lavage Auto, Spectacles & Autres)
    $knownServices = ['room_booking', 'reservation_deposit', 'restaurant', 'pressing', 'car_wash', 'show_ticket'];
    $byService = [
        'room_booking' => $transactions->whereIn('service_type', ['room_booking', 'reservation_deposit'])->sum('amount_paid'),
        'restaurant'   => $transactions->where('service_type', 'restaurant')->sum('amount_paid'),
        'pressing'     => $transactions->where('service_type', 'pressing')->sum('amount_paid'),
        'car_wash'     => $transactions->where('service_type', 'car_wash')->sum('amount_paid'),
        'show_ticket'  => $transactions->where('service_type', 'show_ticket')->sum('amount_paid'),
        'other'        => $transactions->whereNotIn('service_type', $knownServices)->sum('amount_paid'),
    ];

    $grandTotal = $transactions->sum('amount_paid');

    // 3. Sorties de caisse du jour (Dépenses Petite Caisse & Acomptes sur salaire RH)
    $dailyExpenses = \Illuminate\Support\Facades\DB::table('expenses')
        ->whereDate('expense_date', $date)
        ->orderBy('created_at')
        ->get();

    $totalExpenses = $dailyExpenses->sum('amount');
    $cashExpenses  = $dailyExpenses->filter(
        fn($e) => in_array(mb_strtolower(trim((string) $e->payment_method)), ['espèces', 'especes', 'cash'])
    )->sum('amount');

    // 4. Soldes nets de clôture
    $netCashInDrawer = $byMethod['cash'] - $cashExpenses;
    $netDailyTotal   = $grandTotal - $totalExpenses;

    return view('billing.daily_close', compact(
        'date',
        'transactions',
        'byMethod',
        'byService',
        'grandTotal',
        'dailyExpenses',
        'totalExpenses',
        'cashExpenses',
        'netCashInDrawer',
        'netDailyTotal'
    ));
}


    /**
     * Libération officielle de la chambre (Check-out) avec verrouillage des impayés et passage en ménage
     */
    public function checkoutRoom(int $roomId)
    {
        $room = Room::findOrFail($roomId);

        if ($room->status !== 'occupied') {
            return back()->with('error', "La Chambre {$room->number} n'est pas actuellement occupée.");
        }

        // 1. Calculer l'engagement total actuel du séjour
        $activeCard = DB::table('magnetic_cards')
            ->where('room_id', $room->id)
            ->where('is_active', 1)
            ->latest('id')
            ->first();

        $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;
        $stayStartDate = $activeCard ? Carbon::parse($activeCard->created_at) : now()->startOfDay();

        $roomCost = $room->price_per_night * $days;

        $restoCost = Order::where('room_id', $room->id)
            ->where(function ($q) {
                $q->whereNotIn('status', ['paid', 'completed'])
                  ->orWhereNull('status');
            })
            ->sum('total_amount');

        $extraServicesCost = \App\Models\ExtraService::where('room_id', $room->id)
            ->where('payment_status', 'unpaid')
            ->sum('total_amount');

        // Cumul des Activités Annexes (Piscine, Spa, Navette, Coworking...) non soldées
        $activitiesCost = DB::table('activity_bookings')
            ->where('room_id', $room->id)
            ->where('payment_status', 'unpaid')
            ->sum('total_amount');

        $totalCost = $roomCost + $restoCost + $extraServicesCost + $activitiesCost;

        // 2. Calculer tout ce qui a été réglé (arrhes de réservation + règlements effectués pendant le séjour)
        $checkedInReservation = Reservation::where('room_id', $room->id)
            ->where('status', 'checked_in')
            ->latest('id')
            ->first();

        $reservationDeposit = $checkedInReservation ? (float) $checkedInReservation->deposit_paid : 0;

        $cutoffDate = $checkedInReservation
            ? Carbon::parse($checkedInReservation->created_at)->addSeconds(5)
            : $stayStartDate->copy()->subSeconds(5);

        $stayPayments = Billing::where('room_id', $room->id)
            ->where('service_type', 'room_booking')
            ->where('status', 'completed')
            ->where('created_at', '>', $cutoffDate)
            ->sum('amount_paid');

        $totalPaid = $reservationDeposit + $stayPayments;
        $remaining = max(0, $totalCost - $totalPaid);

        // 3. Verrouillage de sécurité : Interdire le Check-out s'il reste un impayé
        if ($remaining > 0) {
            $resteFormate = number_format($remaining, 0, ',', ' ');
            return back()->with('error', "Check-out bloqué : il reste un solde impayé de {$resteFormate} F sur la Chambre {$room->number}. Veuillez d'abord encaisser le règlement.");
        }

        // 4. Exécution atomique de la libération et envoi en Gouvernance / Ménage
        DB::transaction(function () use ($room, $checkedInReservation) {
            // Désactiver la carte magnétique
            DB::table('magnetic_cards')
                ->where('room_id', $room->id)
                ->where('is_active', 1)
                ->update([
                    'is_active'  => 0,
                    'updated_at' => now(),
                ]);

            // Clôturer la réservation associée si elle existe
            if ($checkedInReservation) {
                $checkedInReservation->update(['status' => 'completed']);
            }

            // Passer la chambre en 'cleaning' (À nettoyer) pour le service d'étage
            $room->update(['status' => 'cleaning']);
        });

        return redirect()->route('billing.index')
            ->with('success', "Check-out validé : la Chambre {$room->number} est passée en 'À nettoyer' (Gouvernance) et sa carte magnétique a été désactivée.");
    }


}
