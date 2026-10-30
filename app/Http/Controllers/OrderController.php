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
    // Afficher le formulaire de prise de commande
    public function create()
    {
        $menuItems = MenuItem::where('is_available', true)->get();
        $tables = RestaurantTable::all();
        // On récupère uniquement les chambres occupées pour pouvoir leur imputer des frais
        $rooms = Room::where('status', 'occupied')->get();

        return view('orders.create', compact('menuItems', 'tables', 'rooms'));
    }

      // Enregistrer la commande en base de données
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_table_id' => 'required|exists:restaurant_tables,id',
            'payment_method' => 'required|string',
            'room_id' => 'required_if:payment_method,room|exists:rooms,id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:0', // Changé à min:0 pour tolérer la grille complète
        ]);

        // Filtrer les articles pour ne garder que ceux qui ont une quantité supérieure à 0
        $filteredItems = array_filter($request->items, function ($item) {
            return isset($item['quantity']) && (int)$item['quantity'] > 0;
        });

        // Sécurité : Si aucune quantité n'a été saisie sur toute la grille, on lève une alerte
        if (empty($filteredItems)) {
            return back()->withErrors(['items' => 'Veuillez sélectionner au moins un article avec une quantité supérieure à zéro.'])->withInput();
        }

        // 1. Création de la commande principale
        $order = Order::create([
            'restaurant_table_id' => $request->restaurant_table_id,
            'room_id' => $request->payment_method === 'room' ? $request->room_id : null,
            'status' => $request->payment_method === 'room' ? 'charged_to_room' : 'paid',
            'total_amount' => 0, // Sera calculé juste après
        ]);

        $totalAmount = 0;

        // 2. Enregistrement de chaque ligne de la commande filtrée
        foreach ($filteredItems as $itemData) {
            $menuItem = MenuItem::find($itemData['id']);
            $linePrice = $menuItem->price * (int)$itemData['quantity'];
            $totalAmount += $linePrice;

            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $menuItem->id,
                'quantity' => (int)$itemData['quantity'],
                'price_at_order' => $menuItem->price,
            ]);
        }

        // 3. Mise à jour du montant total réel de la commande
        $order->update(['total_amount' => $totalAmount]);

        $msg = "Commande enregistrée avec succès ! Montant Total : " . number_format($totalAmount, 0, ',', ' ') . " F.";
        if ($order->status === 'charged_to_room') {
            $room = Room::find($request->room_id);
            $msg .= " Le montant a été ajouté à la note de la {$room->number}.";
        }

        // Renvoie le message et l'identifiant pour le bouton d'impression
        return redirect()->route('orders.create')
            ->with('success', $msg)
            ->with('order_id', $order->id);
    }


        public function showReceipt(Order $order)
    {
        // Charge la commande avec ses lignes d'articles et l'emplacement
        $order->load(['restaurantTable']);
        $orderItems = \App\Models\OrderItem::where('order_id', $order->id)->with('menuItem')->get();
        $room = $order->room_id ? \App\Models\Room::find($order->room_id) : null;

        return view('orders.receipt', compact('order', 'orderItems', 'room'));
    }

public function createMenuItem()
{
    return view('admin.menu.create');
}

public function storeMenuItem(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'location_type' => 'required|string|in:restaurant,bar_piscine',
    ]);

    MenuItem::create($request->all());

    return redirect()->route('admin.menu.create')->with('success', "L'article \"{$request->name}\" a été ajouté au menu.");
}


}
