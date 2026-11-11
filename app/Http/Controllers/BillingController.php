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
   // 6. Calcul précis des restes à payer par séjour
    foreach ($billings as $billing) {
        $remaining = 0;

        if ($billing->service_type === 'room_booking' && $billing->room) {
            // Si la chambre est déjà libérée ('available'), le séjour est clôturé et soldé
            if ($billing->room->status === 'available') {
                $billing->remaining_to_pay = 0;
            } else {
                $activeCard = \Illuminate\Support\Facades\DB::table('magnetic_cards')
                    ->where('room_id', $billing->room_id)
                    ->latest()
                    ->first();

                $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;
                $stayStartDate = $activeCard ? $activeCard->created_at : $billing->created_at->startOfDay();
                $totalCost = $billing->room->price_per_night * $days;

                $totalPaidForRoom = \App\Models\Billing::where('room_id', $billing->room_id)
                    ->where('service_type', 'room_booking')
                    ->where('created_at', '>=', $stayStartDate)
                    ->sum('amount_paid');

                $remaining = $totalCost - $totalPaidForRoom;
                $billing->remaining_to_pay = $remaining > 0 ? $remaining : 0;
            }
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


}
