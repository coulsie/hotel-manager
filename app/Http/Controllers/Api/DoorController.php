<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MagneticCard;
use App\Models\RoomAccessLog;
use App\Models\Room;
use Illuminate\Http\Request;

class DoorController extends Controller
{
    public function verifyAccess(Request $request)
    {
        // 1. Validation des données envoyées par l'appareil IoT
        $request->validate([
            'card_uid' => 'required|string',
            'room_number' => 'required|string'
        ]);

        // 2. Recherche de la chambre
        $room = Room::where('number', $request->room_number)->first();

        if (!$room) {
            return response()->json(['status' => 'lock', 'message' => 'Chambre inconnue.'], 404);
        }

        // 3. Vérification de la carte magnétique
        $card = MagneticCard::where('card_uid', $request->card_uid)
            ->where('room_id', $room->id)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        if ($card) {
            // Accès autorisé -> Enregistrement du log
            RoomAccessLog::create([
                'room_id' => $room->id,
                'magnetic_card_id' => $card->id,
                'action' => 'access_granted'
            ]);

            return response()->json([
                'status' => 'unlock',
                'message' => 'Accès autorisé',
                'token' => $card->security_token
            ], 200);
        }

        // Accès refusé -> Enregistrement de l'échec pour suivi à distance
        RoomAccessLog::create([
            'room_id' => $room->id,
            'magnetic_card_id' => null,
            'action' => 'access_denied'
        ]);

        return response()->json(['status' => 'lock', 'message' => 'Carte invalide ou expirée.'], 403);
    }
}
