<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use Carbon\Carbon;

class GuestPortalController extends Controller
{
    /**
     * Page publique mobile ouverte par le client en scannant le QR Code de sa chambre
     */
       public function showRoomPortal(int $roomId)
    {
        $room = Room::findOrFail($roomId);

        // 1. Sécurité : Le portail ne s'active que si la chambre est occupée
        if ($room->status !== 'occupied') {
            return view('guest.inactive', compact('room'));
        }

        // 2. Récupérer la carte du restaurant (uniquement les plats disponibles)
        $menuQuery = DB::table('menu_items')->orderBy('name');
        if (\Illuminate\Support\Facades\Schema::hasColumn('menu_items', 'is_available')) {
            $menuQuery->where('is_available', 1);
        }
        $menuItems = $menuQuery->get();

        // 3. Déterminer le début exact du séjour actuel (Réservation checked_in -> Carte active -> Aujourd'hui)
        $activeReservation = \App\Models\Reservation::where('room_id', $room->id)
            ->where('status', 'checked_in')
            ->latest('id')
            ->first();

        $activeCard = DB::table('magnetic_cards')
            ->where('room_id', $room->id)
            ->where('is_active', 1)
            ->latest('id')
            ->first();

        if ($activeReservation) {
            $since = $activeReservation->created_at;
        } elseif ($activeCard) {
            $since = $activeCard->created_at;
        } else {
            $since = now()->startOfDay();
        }

        // 4. Suivi isolé des commandes et demandes du client actuel uniquement
        $myOrders = DB::table('orders')
            ->where('room_id', $room->id)
            ->where('created_at', '>=', $since)
            ->orderByDesc('created_at')
            ->get();

        $myRequests = DB::table('room_requests')
            ->where('room_id', $room->id)
            ->where('created_at', '>=', $since)
            ->orderByDesc('created_at')
            ->get();

        return view('guest.portal', compact('room', 'menuItems', 'myOrders', 'myRequests'));
    }


    /**
     * Enregistrer une commande Room Service passée depuis le téléphone du client
     */
       public function storeRoomOrder(Request $request, int $roomId)
    {
        $room = Room::findOrFail($roomId);

        if ($room->status !== 'occupied') {
            return back()->with('error', 'Cette chambre n\'est pas ouverte au Room Service.');
        }

        $quantities = $request->input('quantities', []);
        if (!is_array($quantities)) {
            return back()->with('error', 'Format de commande invalide.');
        }

        $selectedItems = [];
        $totalAmount = 0;
        $hasAvailabilityCol = \Illuminate\Support\Facades\Schema::hasColumn('menu_items', 'is_available');

        foreach ($quantities as $menuItemId => $qty) {
            $qty = min(20, max(0, (int) $qty)); // Plafond de sécurité à 20 unités par article
            if ($qty > 0) {
                $itemQuery = DB::table('menu_items')->where('id', (int) $menuItemId);
                if ($hasAvailabilityCol) {
                    $itemQuery->where('is_available', 1);
                }

                $item = $itemQuery->first();
                if ($item) {
                    $lineTotal = (float) $item->price * $qty;
                    $totalAmount += $lineTotal;
                    $selectedItems[] = [
                        'menu_item_id' => $item->id,
                        'quantity'     => $qty,
                        'unit_price'   => $item->price,
                    ];
                }
            }
        }

        if (empty($selectedItems)) {
            return back()->with('error', 'Veuillez sélectionner au moins un plat ou une boisson disponible.');
        }

        DB::transaction(function () use ($room, $totalAmount, $selectedItems) {
            $orderId = DB::table('orders')->insertGetId([
                'room_id'             => $room->id,
                'restaurant_table_id' => null,
                'total_amount'        => $totalAmount,
                'status'              => 'pending', // Apparaît immédiatement en cuisine et dans l'encours chambre
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            foreach ($selectedItems as $line) {
                DB::table('order_items')->insert([
                    'order_id'       => $orderId,
                    'menu_item_id'   => $line['menu_item_id'],
                    'quantity'       => $line['quantity'],
                    'price_at_order' => $line['unit_price'],
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        });

        return back()->with('success', 'Votre commande Room Service a été transmise à la cuisine et ajoutée à votre chambre !');
    }

    /**
     * Envoyer une demande rapide à la Gouvernance / Réception (Serviettes, Ménage, Panne...)
     */
        public function storeRoomRequest(Request $request, int $roomId)
    {
        $room = Room::findOrFail($roomId);

        if ($room->status !== 'occupied') {
            return back()->with('error', 'Action impossible : cette chambre n\'est pas occupée.');
        }

        $validated = $request->validate([
            'type'    => 'required|string|max:50',
            'details' => 'nullable|string|max:500',
        ]);

        $type    = trim($validated['type']);
        $details = isset($validated['details']) ? trim($validated['details']) : null;

        // Anti-spam : vérifier si une demande identique est déjà en attente pour cette chambre
        $alreadyPending = DB::table('room_requests')
            ->where('room_id', $room->id)
            ->where('type', $type)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return back()->with('error', 'Une demande de ce type est déjà en cours de traitement pour votre chambre.');
        }

        DB::table('room_requests')->insert([
            'room_id'    => $room->id,
            'type'       => $type,
            'details'    => $details ?: null,
            'status'     => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Votre demande a bien été transmise à notre équipe d\'étage / réception.');
    }

    /**
     * Page Administration / Réception : Afficher & Imprimer les QR Codes de toutes les chambres
     */
    public function qrCodesIndex()
    {
        $rooms = Room::orderBy('number')->get();
        $pendingRequests = DB::table('room_requests')
            ->join('rooms', 'room_requests.room_id', '=', 'rooms.id')
            ->where('room_requests.status', '!=', 'completed')
            ->select('room_requests.*', 'rooms.number as room_number')
            ->orderByDesc('room_requests.created_at')
            ->get();

        return view('guest.qrcodes', compact('rooms', 'pendingRequests'));
    }

    /**
     * Marquer une demande client en chambre comme traitée
     */
    public function completeRequest(int $id)
    {
        DB::table('room_requests')->where('id', $id)->update([
            'status'     => 'completed',
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Demande client marquée comme traitée.');
    }

/**
     * Vérifie que la chambre est bien occupée par un client en cours de séjour
     * et retourne la date de début du séjour actuel pour isoler les données.
     */
    private function verifyActiveStay(Room $room): Carbon
    {
        $activeReservation = Reservation::where('room_id', $room->id)
            ->where('status', 'checked_in')
            ->latest('id')
            ->first();

        $activeCard = DB::table('magnetic_cards')
            ->where('room_id', $room->id)
            ->where('is_active', 1)
            ->latest('id')
            ->first();

        // Bloquer l'accès si la chambre est libre, sale, en maintenance ou sans séjour actif
        if ($room->status !== 'occupied' && !$activeReservation && !$activeCard) {
            abort(403, 'Accès refusé : cette chambre n\'est actuellement attribuée à aucun séjour actif.');
        }

        // Déterminer le début exact du séjour actuel (pour masquer l'historique des anciens clients)
        if ($activeReservation) {
            return Carbon::parse($activeReservation->created_at);
        }

        if ($activeCard) {
            return Carbon::parse($activeCard->created_at);
        }

        return Carbon::today();
    }

}
