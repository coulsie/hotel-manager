<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\RestaurantTable;
use App\Models\Room;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Affiche le formulaire de prise de commande (Restaurant / Bar).
     */
    public function create()
    {
        $menuItems = MenuItem::where('is_available', true)->get();
        $tables = RestaurantTable::all();
        // On récupère uniquement les chambres occupées pour pouvoir leur imputer des frais
        $rooms = Room::where('status', 'occupied')->get();

        return view('orders.create', compact('menuItems', 'tables', 'rooms'));
    }


    public function destroyMenuItem(MenuItem $menuItem)
    {
        $itemName = $menuItem->name;
        $menuItem->delete();

        return redirect()->route('admin.menu.create')
            ->with('success', "L'article \"{$itemName}\" a été supprimé du menu.");
    }


    /**
     * Enregistre la commande en base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_table_id' => 'required|exists:restaurant_tables,id',
            'payment_method'      => 'required|string',
            'room_id'             => 'required_if:payment_method,room|exists:rooms,id',
            'items'               => 'required|array|min:1',
            'items.*.id'          => 'required|exists:menu_items,id',
            'items.*.quantity'    => 'required|integer|min:0',
        ]);

        // Filtrer les articles pour ne garder que ceux qui ont une quantité supérieure à 0
        $filteredItems = array_filter($request->items, function ($item) {
            return isset($item['quantity']) && (int)$item['quantity'] > 0;
        });

        // Sécurité : Si aucune quantité n'a été saisie sur toute la grille, on lève une alerte
        if (empty($filteredItems)) {
            return back()
                ->withErrors(['items' => 'Veuillez sélectionner au moins un article avec une quantité supérieure à zéro.'])
                ->withInput();
        }

        // 1. Création de la commande principale (en attente de règlement à la caisse)
        $order = Order::create([
            'restaurant_table_id' => $request->restaurant_table_id,
            'room_id'             => $request->payment_method === 'room' ? $request->room_id : null,
            'status'              => $request->payment_method === 'room' ? 'charged_to_room' : 'pending',
            'total_amount'        => 0,
        ]);

        $totalAmount = 0;

        // 2. Enregistrement de chaque ligne de la commande filtrée
        foreach ($filteredItems as $itemData) {
            $menuItem = MenuItem::find($itemData['id']);
            $linePrice = $menuItem->price * (int)$itemData['quantity'];
            $totalAmount += $linePrice;

            OrderItem::create([
                'order_id'       => $order->id,
                'menu_item_id'   => $menuItem->id,
                'quantity'       => (int)$itemData['quantity'],
                'price_at_order' => $menuItem->price,
            ]);
        }

        // 3. Mise à jour du montant total réel de la commande
        $order->update(['total_amount' => $totalAmount]);

        $msg = "Commande enregistrée avec succès ! Montant Total : " . number_format($totalAmount, 0, ',', ' ') . " F.";
        if ($order->status === 'charged_to_room') {
            $room = Room::find($request->room_id);
            $msg .= " Le montant a été ajouté à la note de la chambre {$room->number}.";
        }

        // Renvoie le message et l'identifiant pour le bouton d'impression
        return redirect()->route('orders.create')
            ->with('success', $msg)
            ->with('order_id', $order->id);
    }

    /**
     * Affiche le reçu d'une commande.
     */
    public function showReceipt(Order $order)
    {
        $order->load(['restaurantTable']);
        $orderItems = OrderItem::where('order_id', $order->id)->with('menuItem')->get();
        $room = $order->room_id ? Room::find($order->room_id) : null;

        return view('orders.receipt', compact('order', 'orderItems', 'room'));
    }

    /**
     * Affiche le formulaire d'administration du menu et la liste des articles existants.
     */
    public function createMenuItem()
    {
        $menuItems = MenuItem::orderBy('location_type')->orderBy('name')->get();
        return view('admin.menu.create', compact('menuItems'));
    }

    /**
     * Enregistre un nouvel article dans le menu (Restaurant ou Bar Piscine).
     */
    public function storeMenuItem(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:191',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'location_type' => 'required|string|in:restaurant,bar_piscine',
        ]);

        // Par défaut, un nouvel article créé est disponible (1)
        $validated['is_available'] = $request->has('is_available') ? (bool)$request->is_available : true;

        MenuItem::create($validated);

        return redirect()->route('admin.menu.create')
            ->with('success', "L'article \"{$validated['name']}\" a été ajouté au menu avec succès.");
    }

        public function updateMenuItem(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:191',
            'location_type' => 'required|string|in:restaurant,bar_piscine',
            'price'         => 'required|numeric|min:0',
            'is_available'  => 'required|boolean',
        ]);

        $menuItem->update($validated);

        return redirect()->route('admin.menu.create')
            ->with('success', "L'article \"{$menuItem->name}\" a été mis à jour.");
    }

}
