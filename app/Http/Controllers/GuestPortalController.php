<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestPortalController extends Controller
{
    /**
     * Page publique mobile ouverte par le client en scannant le QR Code de sa chambre
     */
    public function showRoomPortal(int $roomId)
    {
        $room = Room::findOrFail($roomId);

        // Sécurité : Le portail ne s'active que si la chambre est occupée
        if ($room->status !== 'occupied') {
            return view('guest.inactive', compact('room'));
        }

        // Récupérer la carte du restaurant (articles disponibles)
       

        // Récupérer la carte du restaurant (articles disponibles)
        $menuItems = DB::table('menu_items')
            ->orderBy('name')
            ->get();


        // Suivi des demandes et commandes récentes du client durant son séjour actuel
        $activeCard = DB::table('magnetic_cards')
            ->where('room_id', $room->id)
            ->where('is_active', 1)
            ->latest()
            ->first();

        $since = $activeCard ? $activeCard->created_at : now()->startOfDay();

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
        $selectedItems = [];
        $totalAmount = 0;

        foreach ($quantities as $menuItemId => $qty) {
            $qty = (int) $qty;
            if ($qty > 0) {
                $item = DB::table('menu_items')->where('id', $menuItemId)->first();
                if ($item) {
                    $lineTotal = $item->price * $qty;
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
            return back()->with('error', 'Veuillez sélectionner au moins un plat ou une boisson.');
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
            return back()->with('error', 'Chambre inactive.');
        }

        $validated = $request->validate([
            'type'    => 'required|string|max:50',
            'details' => 'nullable|string|max:500',
        ]);

        DB::table('room_requests')->insert([
            'room_id'    => $room->id,
            'type'       => $validated['type'],
            'details'    => $validated['details'] ?? null,
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
}
