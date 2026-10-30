<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\MagneticCard;
use Illuminate\Support\Str;

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
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'card_uid' => 'required|string|unique:magnetic_cards,card_uid',
            'days' => 'required|integer|min:1'
        ]);

        // 1. Mettre à jour le statut de la chambre à 'occupée'
        $room = Room::find($request->room_id);
        $room->update(['status' => 'occupied']);

        // 2. Générer un jeton de sécurité unique pour l'IoT et enregistrer la carte
        // CORRIGÉ : On force la conversion de $request->days en entier (int)
        $room_id = $room->id;
        MagneticCard::create([
            'card_uid' => $request->card_uid,
            'security_token' => Str::random(32),
            'room_id' => $room_id,
            'expires_at' => now()->addDays((int) $request->days),
            'is_active' => true
        ]);

        return redirect()->route('checkin.form')->with('success', "Check-in réussi ! La carte virtuelle a été associée avec succès à la {$room->number}.");
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

}
