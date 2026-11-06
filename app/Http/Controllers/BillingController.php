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
        // 1. Récupération des commandes de restaurant en attente
        $pendingOrders = \App\Models\Order::with(['restaurantTable', 'room'])
            ->whereNotIn('status', ['paid', 'completed'])
            ->latest()
            ->get();

        // 2. Calcul dynamique et exact des encours d'hébergement
        $occupiedRooms = \App\Models\Room::where('status', 'occupied')->get();
        $pendingRooms = [];

        foreach ($occupiedRooms as $room) {
            // Extraction de la carte active associée à la chambre
            $activeCard = \Illuminate\Support\Facades\DB::table('magnetic_cards')
                ->where('room_id', $room->id)
                ->where('is_active', 1)
                ->latest()
                ->first();

            // LECTURE DIRECTE : On prend la durée exacte enregistrée, sinon 1 nuitée par défaut
            $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;

            // Frais totaux de la chambre (Prix de la nuitée × Durée brute simplifiée)
            $totalCost = $room->price_per_night * $days;

            // Somme totale des acomptes déjà perçus en caisse pour cette chambre
            $amountPaid = \App\Models\Billing::where('room_id', $room->id)
                ->where('service_type', 'room_booking')
                ->sum('amount_paid');

            // Calcul du reste à solder
            $remainingToPay = $totalCost - $amountPaid;

            // On ajoute uniquement si le compte client présente un solde débiteur (reste à payer > 0)
            if ($remainingToPay > 0) {
                $pendingRooms[] = [
                    'id'          => $room->id,
                    'room_number' => $room->number,
                    'room_type'   => $room->type,
                    'created_at'  => $activeCard ? \Carbon\Carbon::parse($activeCard->created_at) : now(),
                    'days'        => $days,
                    'total_cost'  => $totalCost,
                    'amount_paid' => $amountPaid,
                    'remaining'   => $remainingToPay
                ];
            }
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
    $query = \App\Models\Billing::with(['room', 'order.restaurantTable', 'eventTicket.event']);

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
        $query->where(function($q) use ($search) {
            $q->where('receipt_number', 'LIKE', "%{$search}%")
              ->orWhere('payment_method', 'LIKE', "%{$search}%")
              ->orWhereHas('room', function($r) use ($search) {
                  $r->where('number', 'LIKE', "%{$search}%");
              });
        });
    }

    // 🌟 4. CALCUL DU TOTAL DES RECETTES DE LA PÉRIODE FILTRÉE (Avant pagination)
    $periodTotal = $query->sum('amount_paid');

    // 5. Récupération des transactions avec pagination (15 par page pour stopper les lenteurs)
    $billings = $query->latest()->paginate(15);

    // 6. Conservation de votre logique de calcul des restes à payer
    foreach ($billings as $billing) {
        $remaining = 0;

        if ($billing->service_type === 'room_booking' && $billing->room) {
            $activeCard = \Illuminate\Support\Facades\DB::table('magnetic_cards')
                ->where('room_id', $billing->room_id)
                ->latest()
                ->first();

            $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;
            $totalCost = $billing->room->price_per_night * $days;

            $totalPaidForRoom = \App\Models\Billing::where('room_id', $billing->room_id)
                ->where('service_type', 'room_booking')
                ->sum('amount_paid');

            $remaining = $totalCost - $totalPaidForRoom;
            $billing->remaining_to_pay = $remaining > 0 ? $remaining : 0;
        } else {
            $billing->remaining_to_pay = 0;
        }
    }

    // 7. Envoi à la vue avec la variable du total global de la période
    return view('billing.payments', compact('billings', 'periodTotal'));
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

    // 1. Récupération de la carte magnétique active pour avoir le nombre exact de jours (duration_days)
    $activeCard = \Illuminate\Support\Facades\DB::table('magnetic_cards')
        ->where('room_id', $room->id)
        ->latest()
        ->first();

    // Utilisation de duration_days (comme sur le grand livre) au lieu de days
    $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;

    // Coût théorique initial de l'hébergement
    $roomCost = $room->price_per_night * $days;

    // 2. On récupère toutes les commandes restaurant directement liées à cette chambre
    $orders = \App\Models\Order::where('room_id', $room->id)->with('restaurantTable')->get();
    $restaurantTotal = $orders->sum('total_amount');

    // Totaux annexes
    $showsTotal = 0;

    // 3. Calcul du COÛT TOTAL global de la facture (Hébergement + Restaurant)
    $totalInvoice = $roomCost + $restaurantTotal + $showsTotal;

    // 4. Calcul de l'ACOMPTE (Somme déjà payée par le client pour ce séjour)
    $alreadyPaid = \App\Models\Billing::where('room_id', $room->id)
        ->where('service_type', 'room_booking')
        ->where('status', 'completed')
        ->sum('amount_paid');

    // 5. Calcul du RESTE À PAYER (Solde dû pour clôturer)
    $remainingToPay = $totalInvoice - $alreadyPaid;
    if ($remainingToPay < 0) {
        $remainingToPay = 0;
    }

    // Renvoie vers la vue avec toutes les variables synchronisées
    return view('billing.room_invoice', compact(
        'room',
        'roomCost',
        'orders',
        'restaurantTotal',
        'showsTotal',
        'totalInvoice',
        'alreadyPaid',
        'remainingToPay',
        'days'
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

        $billing = Billing::create([
            'receipt_number' => Billing::generateReceiptNumber(),
            'room_id'        => $room->id,
            'service_type'   => 'room_booking',
            'amount_paid'    => $request->input('amount_paid'),
            'payment_method' => $request->input('payment_method'),
            'status'         => 'completed'
        ]);

        // Optionnel : Si le montant payé solde complètement la facture,
        // vous pouvez ajouter ici une logique pour libérer la chambre ou désactiver la carte magnétique.

        return redirect()->route('billing.payments')->with('success', "Encaissement Hébergement validé. Reçu n° {$billing->receipt_number}");
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

}
