<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Order;
use App\Models\Room;
use App\Models\EventTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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

        // Cumul de TOUTES les consommations annexes (Restaurant & Extras) non soldées sur cette chambre
        $extrasCost = \App\Models\Order::where('room_id', $room->id)
            ->where(function ($q) {
                $q->whereNotIn('status', ['paid', 'completed'])
                  ->orWhereNull('status');
            })
            ->sum('total_amount');

        // Total cumulé (Chambre + Extras Restaurant)
        $totalCost = $roomCost + $extrasCost;

        // Somme déjà réglée pour ce séjour (avec une marge de sécurité de 5 secondes pour la création)
        $amountPaid = \App\Models\Billing::where('room_id', $room->id)
            ->where('service_type', 'room_booking')
            ->where('status', 'completed')
            ->where('created_at', '>=', $stayStartDate->copy()->subSeconds(5))
            ->sum('amount_paid');

        $remainingToPay = max(0, $totalCost - $amountPaid);

        // Ajout de toutes les chambres occupées dans le tableau de suivi
        $pendingRooms[] = [
            'id'            => $room->id,
            'room_number'   => $room->number,
            'room_type'     => $room->type,
            'created_at'    => $stayStartDate,
            'checkout_date' => $checkoutDate,
            'days'          => $days,
            'room_cost'     => $roomCost,
            'extras_cost'   => $extrasCost,
            'total_cost'    => $totalCost,
            'amount_paid'   => $amountPaid,
            'remaining'     => $remainingToPay
        ];
    }

    return view('billing.index', compact('pendingOrders', 'pendingRooms'));
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

    // 4. Récupération des transactions filtrées et calcul du total global
    $allBillings = $query->latest()->get();
    $periodTotal = $allBillings->sum('amount_paid');

    // 5. REGROUPEMENT PAR DOSSIER CHAMBRE (Hébergement + Resto lié à la chambre)
    $roomAccounts = [];
    $roomBillings = $allBillings->whereNotNull('room_id')->groupBy('room_id');

    foreach ($roomBillings as $roomId => $transactions) {
        $room = $transactions->first()->room;
        if (!$room) {
            continue;
        }

        // Récupération de la durée du séjour
        $activeCard = \Illuminate\Support\Facades\DB::table('magnetic_cards')
            ->where('room_id', $roomId)
            ->latest()
            ->first();

        $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;
        $roomBaseCost = $room->price_per_night * $days;

        // Commandes restaurant imputées sur cette chambre
        $roomOrders = \App\Models\Order::where('room_id', $roomId)
            ->with('restaurantTable')
            ->get();
        $restoTotal = $roomOrders->sum('total_amount');

        // Engagement global (Chambre + Extras restaurant)
        $totalEngagement = $roomBaseCost + $restoTotal;

        // Somme totale déjà encaissée pour cette chambre (acomptes, soldes, repas payés)
        $totalPaid = $transactions->sum('amount_paid');

        // Calcul du reste à payer (0 si la chambre est déjà libérée)
        $remaining = ($room->status === 'available') ? 0 : max(0, $totalEngagement - $totalPaid);

        $roomAccounts[] = [
            'room'             => $room,
            'days'             => $days,
            'room_cost'        => $roomBaseCost,
            'resto_cost'       => $restoTotal,
            'orders'           => $roomOrders,
            'total_engagement' => $totalEngagement,
            'total_paid'       => $totalPaid,
            'remaining'        => $remaining,
            'transactions'     => $transactions, // Tous les reçus individuels de cette chambre
        ];
    }

    // 6. VENTES DIRECTES HORS CHAMBRE (Clients de passage restaurant & billetterie)
    $directTransactions = $allBillings->whereNull('room_id');

    // 7. Envoi vers la vue avec les dossiers groupés
    return view('billing.payments', compact('roomAccounts', 'directTransactions', 'periodTotal'));
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
    $stayStartDate = $activeCard ? $activeCard->created_at : now()->startOfDay();

    // Coût théorique initial de l'hébergement
    $roomCost = $room->price_per_night * $days;

    // 2. Commandes restaurant du séjour actuel (uniquement celles non payées en cash)
    $orders = \App\Models\Order::where('room_id', $room->id)
        ->where('created_at', '>=', $stayStartDate)
        ->where('status', '!=', 'paid')
        ->with('restaurantTable')
        ->get();

    $restaurantTotal = $orders->sum('total_amount');
    $showsTotal = 0;

    // 3. Coût total global de la facture
    $totalInvoice = $roomCost + $restaurantTotal + $showsTotal;

    // 4. Vérifie si la chambre vient d'être clôturée (statut différent de 'occupied')
    $isPaid = ($room->status !== 'occupied');

    if ($isPaid) {
        // Si clôturée : on récupère uniquement l'acompte initial (le premier paiement s'il était inférieur au total)
        $firstPayment = \App\Models\Billing::where('room_id', $room->id)
            ->where('service_type', 'room_booking')
            ->where('status', 'completed')
            ->where('created_at', '>=', $stayStartDate)
            ->orderBy('created_at', 'asc')
            ->first();

        $alreadyPaid = ($firstPayment && $firstPayment->amount_paid < $totalInvoice) ? $firstPayment->amount_paid : 0;
        $remainingToPay = 0;
    } else {
        // Si en cours : somme des acomptes versés uniquement depuis le début de ce séjour
        $alreadyPaid = \App\Models\Billing::where('room_id', $room->id)
            ->where('service_type', 'room_booking')
            ->where('status', 'completed')
            ->where('created_at', '>=', $stayStartDate)
            ->sum('amount_paid');

        $remainingToPay = max(0, $totalInvoice - $alreadyPaid);
    }

    // 5. Envoi vers la vue avec 'isPaid' inclus dans le compact()
    return view('billing.room_invoice', compact(
        'room',
        'roomCost',
        'orders',
        'restaurantTotal',
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

    // 1. Enregistrement du règlement dans le Grand Livre avec votre numéro de reçu
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
        ->update([
            'status' => 'paid'
        ]);

    // La désactivation du badge et la libération de la chambre se font désormais
    // automatiquement à la fin du séjour (12h00) via la commande planifiée.

    // 3. Retourner sur la facture avec le signal d'impression automatique
    return redirect()->route('billing.room', $room->id)
        ->with('success', "Encaissement Hébergement validé (Reçu n° {$billing->receipt_number}). La chambre reste attribuée jusqu'à la fin du séjour.")
        ->with('print_now', true);


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

    // Ventilation par mode de paiement
    $byMethod = [
        'cash'         => $transactions->where('payment_method', 'cash')->sum('amount_paid'),
        'wave'         => $transactions->where('payment_method', 'wave')->sum('amount_paid'),
        'orange_money' => $transactions->where('payment_method', 'orange_money')->sum('amount_paid'),
        'mtn_momo'     => $transactions->where('payment_method', 'mtn_momo')->sum('amount_paid'),
        'moov_money'   => $transactions->where('payment_method', 'moov_money')->sum('amount_paid'),
        'card'         => $transactions->where('payment_method', 'card')->sum('amount_paid'),
    ];

    // Ventilation par pôle d'activité
    $byService = [
        'room_booking' => $transactions->where('service_type', 'room_booking')->sum('amount_paid'),
        'restaurant'   => $transactions->where('service_type', 'restaurant')->sum('amount_paid'),
        'show_ticket'  => $transactions->where('service_type', 'show_ticket')->sum('amount_paid'),
    ];

    $grandTotal = $transactions->sum('amount_paid');

    return view('billing.daily_close', compact('date', 'transactions', 'byMethod', 'byService', 'grandTotal'));
}

}
