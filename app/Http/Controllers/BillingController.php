<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Order;
use App\Models\Room;
use App\Models\EventTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;

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
    $pendingOrders = \App\Models\Order::with(['restaurantTable', 'room'])
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
    $occupiedRooms = \App\Models\Room::where('status', 'occupied')->get();
    $pendingRooms = [];

    foreach ($occupiedRooms as $room) {
        $activeCard = \Illuminate\Support\Facades\DB::table('magnetic_cards')
            ->where('room_id', $room->id)
            ->where('is_active', 1)
            ->latest()
            ->first();

        $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;
        $stayStartDate = $activeCard ? \Carbon\Carbon::parse($activeCard->created_at) : now()->startOfDay();
        $checkoutDate  = ($activeCard && $activeCard->expires_at)
            ? \Carbon\Carbon::parse($activeCard->expires_at)
            : $stayStartDate->copy()->addDays($days)->setTime(12, 0, 0);

        // Coût des nuitées (Hébergement seul)
        $roomCost = $room->price_per_night * $days;

        // Cumul de TOUTES les commandes Restaurant & Bar non soldées sur cette chambre
        $restoCost = \App\Models\Order::where('room_id', $room->id)
            ->where(function ($q) {
                $q->whereNotIn('status', ['paid', 'completed'])
                  ->orWhereNull('status');
            })
            ->sum('total_amount');

        // Cumul de TOUTES les prestations Pressing & Lavage Auto non soldées sur cette chambre
        $pressingAutoCost = \App\Models\ExtraService::where('room_id', $room->id)
            ->where('payment_status', 'unpaid')
            ->sum('total_amount');

        // Total combiné des extras (Restaurant + Pressing + Lavage Auto)
        $extrasCost = $restoCost + $pressingAutoCost;

        // Total cumulé (Chambre + Extras)
        $totalCost = $roomCost + $extrasCost;

        // 2a. Somme déjà réglée sur place pendant ce séjour (Grand Livre : room_booking)
        $stayPayments = \App\Models\Billing::where('room_id', $room->id)
            ->where('service_type', 'room_booking')
            ->where('status', 'completed')
            ->where('created_at', '>=', $stayStartDate->copy()->subSeconds(5))
            ->sum('amount_paid');

        // 2b. Récupération de l'acompte éventuel d'une réservation convertie en check-in
        // (Cet acompte a déjà été encaissé dans Billing sous 'reservation_deposit' et n'est pas recréé au check-in)
        $checkedInReservation = \App\Models\Reservation::where('room_id', $room->id)
            ->where('status', 'checked_in')
            ->latest('updated_at')
            ->first();

        $reservationDeposit = $checkedInReservation ? (float) $checkedInReservation->deposit_paid : 0;

        // Total cumulé déjà réglé (Acompte réservation + Règlements sur place)
        $amountPaid = $stayPayments + $reservationDeposit;

        // Reste réel à payer par le client
        $remainingToPay = max(0, $totalCost - $amountPaid);

        // Ajout de toutes les chambres occupées dans le tableau de suivi
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

    // 5. REGROUPEMENT PAR DOSSIER CHAMBRE (Règlements sur place + Réservations en cours 'checked_in')
    $roomAccounts = [];

    // On rassemble les IDs des chambres ayant des paiements directs ET celles ayant une réservation en cours (checked_in)
    $billingRoomIds = $allBillings
        ->whereNotNull('room_id')
        ->where('service_type', '!=', 'reservation_deposit')
        ->pluck('room_id');

    $checkedInRoomIds = Reservation::where('status', 'checked_in')->pluck('room_id');
    $allRelevantRoomIds = $billingRoomIds->merge($checkedInRoomIds)->unique();

    foreach ($allRelevantRoomIds as $roomId) {
        $room = \App\Models\Room::find($roomId);
        if (!$room) {
            continue;
        }

        // Paiements sur place hors arrhes futures
        $transactions = $allBillings
            ->where('room_id', $roomId)
            ->where('service_type', '!=', 'reservation_deposit');

        $activeCard = \Illuminate\Support\Facades\DB::table('magnetic_cards')
            ->where('room_id', $roomId)
            ->latest()
            ->first();

        $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;
        $roomBaseCost = $room->price_per_night * $days;

        $roomOrders = \App\Models\Order::where('room_id', $roomId)
            ->with('restaurantTable')
            ->get();

        $roomExtraServices = \App\Models\ExtraService::where('room_id', $roomId)->get();
        $roomOtherBillings = $transactions->where('service_type', '!=', 'room_booking');

        $extrasTotal = $roomOrders->sum('total_amount')
                     + $roomExtraServices->sum('total_amount')
                     + $roomOtherBillings->sum('amount_paid');

        // Engagement global (Chambre + Extras)
        $totalEngagement = $roomBaseCost + $extrasTotal;

        // Récupérer l'acompte de la réservation en cours (checked_in) pour cette chambre
        $checkedInReservation = Reservation::where('room_id', $roomId)
            ->where('status', 'checked_in')
            ->latest('updated_at')
            ->first();

        $reservationDeposit = $checkedInReservation ? (float) $checkedInReservation->deposit_paid : 0;

        // Total déjà payé (Règlements sur place + Acompte de la réservation)
        $totalPaid = $transactions->sum('amount_paid') + $reservationDeposit;

        // Reste à payer (0 si la chambre est déjà libérée)
        $remaining = ($room->status === 'available') ? 0 : max(0, $totalEngagement - $totalPaid);

        // Joindre les reçus d'arrhes de cette chambre pour qu'ils restent consultables/imprimables dans le dossier
        $depositBillings = \App\Models\Billing::where('room_id', $roomId)
            ->where('service_type', 'reservation_deposit')
            ->where('status', 'completed')
            ->latest()
            ->get();

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
            'transactions'        => $transactions->merge($depositBillings),
        ];
    }

    // 5 bis. RUBRIQUE RÉSERVATIONS & ARRHES (Séjours confirmés en attente d'arrivée)
    $reservations = Reservation::with(['customer', 'room'])
        ->where('status', 'confirmed')
        ->orderBy('check_in_date', 'asc')
        ->get();

    $reservationAccounts = [];
    foreach ($reservations as $res) {
        // Récupérer les reçus d'arrhes liés à cette chambre/réservation
        $depositBillings = \App\Models\Billing::where('room_id', $res->room_id)
            ->where('service_type', 'reservation_deposit')
            ->where('status', 'completed')
            ->latest()
            ->get();

        $remainingRes = max(0, $res->total_amount - $res->deposit_paid);

        $reservationAccounts[] = [
            'reservation'  => $res,
            'total_amount' => $res->total_amount,
            'deposit_paid' => $res->deposit_paid,
            'remaining'    => $remainingRes,
            'billings'     => $depositBillings,
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
    $room = \App\Models\Room::findOrFail($roomId);

    // 1. Récupération de la carte magnétique du séjour actuel
    $activeCard = \Illuminate\Support\Facades\DB::table('magnetic_cards')
        ->where('room_id', $room->id)
        ->latest()
        ->first();

    $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;
    $stayStartDate = $activeCard ? \Carbon\Carbon::parse($activeCard->created_at) : now()->startOfDay();

    // Coût théorique initial de l'hébergement
    $roomCost = $room->price_per_night * $days;

    // 2. Commandes restaurant du séjour actuel non payées
    $orders = \App\Models\Order::where('room_id', $room->id)
        ->where(function ($q) {
            $q->whereNotIn('status', ['paid', 'completed'])
              ->orWhereNull('status');
        })
        ->with('restaurantTable')
        ->get();

    $restaurantTotal = $orders->sum('total_amount');

    // 3. Prestations Pressing & Lavage Auto imputées à cette chambre et non payées
    $extraServices = \App\Models\ExtraService::where('room_id', $room->id)
        ->where('payment_status', 'unpaid')
        ->get();

    $extraServicesTotal = $extraServices->sum('total_amount');
    $showsTotal = 0;

    // 4. Coût total global de la facture (Hébergement + Restaurant + Pressing/Lavage Auto)
    $totalInvoice = $roomCost + $restaurantTotal + $extraServicesTotal + $showsTotal;

    // 5. Calcul des sommes déjà versées et du reste à payer
    $alreadyPaid = \App\Models\Billing::where('room_id', $room->id)
        ->where('service_type', 'room_booking')
        ->where('status', 'completed')
        ->where('created_at', '>=', $stayStartDate->copy()->subSeconds(5))
        ->sum('amount_paid');

    $remainingToPay = max(0, $totalInvoice - $alreadyPaid);
    $isPaid = ($remainingToPay <= 0);

    // 6. Envoi vers la vue avec 'extraServices' et 'extraServicesTotal' inclus
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

    // La désactivation du badge et la libération de la chambre se font automatiquement à 12h00
    // ou manuellement via le bouton Check-out anticipé.

    // 4. Redirection vers le Grand Livre (payments.blade.php) avec impression du reçu
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

    // 1. Ventilation par mode de paiement
    $knownMethods = ['cash', 'wave', 'orange_money', 'mtn_momo', 'moov_money', 'card'];
    $byMethod = [
        'cash'         => $transactions->where('payment_method', 'cash')->sum('amount_paid'),
        'wave'         => $transactions->where('payment_method', 'wave')->sum('amount_paid'),
        'orange_money' => $transactions->where('payment_method', 'orange_money')->sum('amount_paid'),
        'mtn_momo'     => $transactions->where('payment_method', 'mtn_momo')->sum('amount_paid'),
        'moov_money'   => $transactions->where('payment_method', 'moov_money')->sum('amount_paid'),
        'card'         => $transactions->where('payment_method', 'card')->sum('amount_paid'),
        'other'        => $transactions->whereNotIn('payment_method', $knownMethods)->sum('amount_paid'),
    ];

    // 2. Ventilation par pôle d'activité (Hébergement, Resto, Pressing, Lavage Auto, Spectacles & Autres)
    $knownServices = ['room_booking', 'restaurant', 'pressing', 'car_wash', 'show_ticket'];
    $byService = [
        'room_booking' => $transactions->where('service_type', 'room_booking')->sum('amount_paid'),
        'restaurant'   => $transactions->where('service_type', 'restaurant')->sum('amount_paid'),
        'pressing'     => $transactions->where('service_type', 'pressing')->sum('amount_paid'),
        'car_wash'     => $transactions->where('service_type', 'car_wash')->sum('amount_paid'),
        'show_ticket'  => $transactions->where('service_type', 'show_ticket')->sum('amount_paid'),
        'other'        => $transactions->whereNotIn('service_type', $knownServices)->sum('amount_paid'),
    ];

    $grandTotal = $transactions->sum('amount_paid');

    return view('billing.daily_close', compact('date', 'transactions', 'byMethod', 'byService', 'grandTotal'));
}



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
        ->latest()
        ->first();

    $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;
    $stayStartDate = $activeCard ? \Carbon\Carbon::parse($activeCard->created_at) : now()->startOfDay();

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

    $totalCost = $roomCost + $restoCost + $extraServicesCost;

    // 2. Calculer tout ce qui a été réglé (sur place + arrhes de réservation)
    $stayPayments = Billing::where('room_id', $room->id)
        ->where('service_type', 'room_booking')
        ->where('status', 'completed')
        ->where('created_at', '>=', $stayStartDate->copy()->subSeconds(5))
        ->sum('amount_paid');

    $checkedInReservation = Reservation::where('room_id', $room->id)
        ->where('status', 'checked_in')
        ->latest('updated_at')
        ->first();

    $reservationDeposit = $checkedInReservation ? (float) $checkedInReservation->deposit_paid : 0;
    $totalPaid = $stayPayments + $reservationDeposit;

    $remaining = max(0, $totalCost - $totalPaid);

    // 3. Verrouillage de sécurité : Interdire le Check-out s'il reste un impayé
    if ($remaining > 0) {
        $resteFormate = number_format($remaining, 0, ',', ' ');
        return back()->with('error', "Check-out bloqué : il reste un solde impayé de {$resteFormate} F sur la Chambre {$room->number}. Veuillez d'abord encaisser le règlement.");
    }

    // 4. Exécution atomique de la libération
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

        // Libérer la chambre (ou la passer en 'cleaning' si vous activez le ménage)
        $room->update(['status' => 'available']);
    });

    return redirect()->route('billing.index')
        ->with('success', "Check-out effectué avec succès : la Chambre {$room->number} est désormais libre et sa carte magnétique a été désactivée.");
}




}
