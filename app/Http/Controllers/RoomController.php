<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\MagneticCard;
use Illuminate\Support\Str;
// 👇 AJOUTEZ CES INSTRUCTIONS D'IMPORTATION ICI
use Illuminate\Support\Facades\DB;       // Résout l'erreur : Class 'DB' is not imported
use App\Models\Billing;                  // Résout l'erreur : Class 'Billing' is not imported
use App\Models\Customer;                 // Résout l'erreur : Model: Customer / App\Models\Customer
class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        //
    }
    // Afficher le formulaire de Check-in
    public function showCheckinForm()
    {
        // On récupère uniquement les chambres disponibles pour y installer un client
        $rooms = Room::where('status', 'available')->get();
        return view('rooms.checkin', compact('rooms'));
    }

    // Traiter l'attribution de la carte magnétique
        // Traiter l'attribution de la carte magnétique
       // Traiter l'attribution de la carte magnétique
        public function processCheckin(Request $request)
    {
        // 1. Validation stricte des données du formulaire
        $request->validate([
            'customer_name'  => 'required|string|max:191',
            'customer_phone' => 'nullable|string|max:50',
            'id_card_number' => 'nullable|string|max:50',
            'room_id'        => 'required|exists:rooms,id',
            'card_uid'       => 'required|string|max:191',
            'days'           => 'required|integer|min:1',
            'payment_type'   => 'required|in:full,partial,later',
            'payment_method' => 'required_unless:payment_type,later|string',
            'amount_paid'    => 'required|numeric|min:0',
        ]);

        // Utilisation d'une transaction de base de données pour tout sécuriser
        DB::beginTransaction();

        try {
            // 2. Création du client dans la table 'customers'
            $customer = Customer::create([
                'full_name'      => $request->input('customer_name'),
                'phone'          => $request->input('customer_phone'),
                'id_card_number' => $request->input('id_card_number'),
            ]);

            // 3. Mise à jour du statut de la Chambre
            $room = Room::findOrFail($request->input('room_id'));
            $room->update([
                'status' => 'occupied',
            ]);

            // 👇 3.5 ENREGISTREMENT ET VALIDATION DE LA CARTE MAGNÉTIQUE (Avec durée directe)
            // On calcule l'expiration à partir du nombre de jours saisis
            $days = intval($request->input('days'));
            $expiresAt = now()->addDays($days)->setTime(12, 0, 0); // Expiration par défaut à midi le jour du départ


            DB::table('magnetic_cards')->insert([
                'card_uid'       => $request->input('card_uid'),
                'security_token' => \Illuminate\Support\Str::random(32), // 👈 CODE ABSOLU MODIFIÉ
                'room_id'        => $room->id,
                'duration_days'  => $days,
                'expires_at'     => $expiresAt,
                'is_active'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);


            // 4. ALIMENTATION DE LA TABLE BILLINGS (Si le paiement n'est pas différé)
            if ($request->input('payment_type') !== 'later' && $request->input('amount_paid') > 0) {
                Billing::create([
                    'receipt_number' => Billing::generateReceiptNumber(),
                    'room_id'        => $room->id,
                    'order_id'       => null,
                    'event_ticket_id'=> null,
                    'service_type'   => 'room_booking',
                    'amount_paid'    => $request->input('amount_paid'),
                    'payment_method' => $request->input('payment_method'),
                    'status'         => 'completed'
                ]);
            }

            DB::commit();

            $msg = "Check-in réussi ! Le client {$customer->full_name} a été enregistré avec succès à la Chambre {$room->number}.";
            if ($request->input('payment_type') === 'later') {
                $msg .= " (Frais imputés sur la note de chambre)";
            } else {
                $msg .= " (Paiement enregistré dans le Grand Livre)";
            }

            return redirect()->route('checkin.form')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', "Une erreur est survenue lors de l'enregistrement : " . $e->getMessage());
        }
    }


        // Afficher la grille de l'état des chambres
    public function statusGrid()
    {
        // On récupère toutes les chambres triées par numéro, avec les informations de leur carte active si elle existe
        $rooms = Room::with(['magneticCards' => function($query) {
            $query->where('is_active', true)->where('expires_at', '>', now());
        }])->orderBy('number', 'asc')->get();

        return view('rooms.status', compact('rooms'));
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|string|max:50|unique:rooms,number',
            'type' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
        ]);

        Room::create([
            'number' => $request->number,
            'type' => $request->type,
            'price_per_night' => $request->price_per_night,
            'status' => 'available',
        ]);

        return redirect()->route('admin.rooms.create')->with('success', "La chambre {$request->number} a été ajoutée avec succès.");
    }

    // 1. Afficher le tableau de bord du ménage avec la liste des chambres
    public function housekeepingIndex()
    {
        // Récupère toutes les chambres triées par numéro
        $rooms = Room::orderBy('number', 'asc')->get();
        return view('housekeeping.index', compact('rooms'));
    }

    // 2. Changer le statut de propreté d'une chambre en un clic
    public function updateHousekeepingStatus(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|string|in:available,occupied,cleaning'
        ]);

        $room->update(['status' => $request->status]);

        return redirect()->route('housekeeping.index')
            ->with('success', "Le statut de la {$room->number} a été mis à jour avec succès.");
    }


        // Afficher l'historique complet des scans IoT
    public function accessLogsIndex()
    {
        $logs = \App\Models\RoomAccessLog::with(['room', 'magneticCard'])
            ->orderBy('scanned_at', 'desc')
            ->paginate(15); // Pagination pour éviter de surcharger l'affichage

        return view('iot.logs', compact('logs'));
    }


public function earlyCheckout(\App\Models\Room $room)
{
    // 1. Vérification de sécurité : empêche le départ si le séjour n'est pas soldé
    $activeCard = \Illuminate\Support\Facades\DB::table('magnetic_cards')
        ->where('room_id', $room->id)
        ->where('is_active', 1)
        ->latest()
        ->first();

    $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;
    $stayStartDate = $activeCard ? \Carbon\Carbon::parse($activeCard->created_at) : now()->startOfDay();

    $roomCost = $room->price_per_night * $days;
    $unpaidOrders = \App\Models\Order::where('room_id', $room->id)
        ->whereNotIn('status', ['paid', 'completed'])
        ->sum('total_amount');

    $totalDue = $roomCost + $unpaidOrders;

    $totalPaid = \App\Models\Billing::where('room_id', $room->id)
        ->where('service_type', 'room_booking')
        ->where('status', 'completed')
        ->where('created_at', '>=', $stayStartDate->copy()->subSeconds(5))
        ->sum('amount_paid');

    if ($totalDue > $totalPaid) {
        $remaining = number_format($totalDue - $totalPaid, 0, ',', ' ');
        return redirect()->route('billing.room', $room->id)
            ->with('error', "Check-out bloqué : Il reste {$remaining} F CFA à solder avant de libérer la Chambre {$room->number}.");
    }

    // 2. Désactivation immédiate de la carte magnétique
    \Illuminate\Support\Facades\DB::table('magnetic_cards')
        ->where('room_id', $room->id)
        ->where('is_active', 1)
        ->update([
            'is_active'  => 0,
            'expires_at' => now(),
            'updated_at' => now(),
        ]);

    // 3. Libération de la chambre (passe en 'cleaning' pour le ménage ou 'available')
    $room->update(['status' => 'cleaning']);

    return redirect()->back()
        ->with('success', "Check-out validé ! La Chambre {$room->number} a été libérée (envoyée au ménage) et son badge RFID est désactivé.");
}


public function extendStay(\Illuminate\Http\Request $request, \App\Models\Room $room)
{
    $request->validate([
        'extra_days' => 'required|integer|min:1|max:60',
    ]);

    $extraDays = (int) $request->input('extra_days');

    $activeCard = \Illuminate\Support\Facades\DB::table('magnetic_cards')
        ->where('room_id', $room->id)
        ->where('is_active', 1)
        ->latest()
        ->first();

    if (!$activeCard) {
        return redirect()->back()->with('error', "Aucune carte magnétique active trouvée pour la Chambre {$room->number}.");
    }

    // Calcul de la nouvelle date d'expiration (ancienne date + X jours à 12h00 précise)
    $currentExpiry = \Carbon\Carbon::parse($activeCard->expires_at);
    $newExpiry     = $currentExpiry->copy()->addDays($extraDays)->setTime(12, 0, 0);
    $newDuration   = ($activeCard->duration_days ?? 1) + $extraDays;

    \Illuminate\Support\Facades\DB::table('magnetic_cards')
        ->where('id', $activeCard->id)
        ->update([
            'duration_days' => $newDuration,
            'expires_at'    => $newExpiry,
            'updated_at'    => now(),
        ]);

    return redirect()->back()->with('success', "Séjour prolongé de {$extraDays} nuit(s) pour la Chambre {$room->number}. Nouveau départ : {$newExpiry->format('d/m/Y à H:i')}.");
}


}
