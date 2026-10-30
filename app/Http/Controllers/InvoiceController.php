<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Order;
use App\Models\ShowBooking;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // 1. Liste des chambres occupées avec encours financier
    public function index()
    {
        $occupiedRooms = Room::where('status', 'occupied')->get();
        return view('billing.index', compact('occupiedRooms'));
    }

    // 2. Génération et affichage de la facture détaillée d'une chambre
    // CORRIGÉ : Ajout du symbole $ devant room pour le typage correct de Laravel (Route Model Binding)
    public function showRoomInvoice(Room $room)
    {
        // Récupérer les commandes du restaurant facturées sur la chambre
        $orders = Order::where('room_id', $room->id)->with('restaurantTable')->get();

        // Récupérer les billets de spectacles imputés sur la chambre
        $showBookings = ShowBooking::where('room_id', $room->id)->with('show')->get();

        // Calcul des totaux
        $roomCost = $room->price_per_night; // Exemple simplifié pour 1 nuitée de test
        $restaurantTotal = $orders->sum('total_amount');
        $showsTotal = $showBookings->sum(function($booking) {
            return $booking->show->ticket_price ?? 0;
        });

        $totalInvoice = $roomCost + $restaurantTotal + $showsTotal;

        return view('billing.room_invoice', compact('room', 'orders', 'showBookings', 'roomCost', 'restaurantTotal', 'showsTotal', 'totalInvoice'));
    }

    // 3. Historique global de tous les paiements enregistrés dans l'hôtel
    public function paymentsHistory()
    {
        // Récupérer toutes les ventes payées au comptoir ou imputées
        $restaurantOrders = Order::with(['restaurantTable', 'room'])->orderBy('created_at', 'desc')->get();
        return view('billing.payments', compact('restaurantOrders'));
    }
}
