<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Customer;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PublicBookingController extends Controller
{
    /**
     * Afficher la vitrine publique de réservation en ligne.
     */
         public function index(Request $request)
    {
        $checkIn  = $request->input('check_in_date', Carbon::tomorrow()->toDateString());
        $checkOut = $request->input('check_out_date', Carbon::tomorrow()->addDay()->toDateString());

        // Sécuriser l'ordre des dates
        if (Carbon::parse($checkOut)->lte(Carbon::parse($checkIn))) {
            $checkOut = Carbon::parse($checkIn)->addDay()->toDateString();
        }

        $nights = max(1, Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)));

        // Chambres déjà réservées sur ce créneau (chevauchement de dates)
        $bookedRoomIds = Reservation::whereNotIn('status', ['cancelled', 'checked_out', 'completed'])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn);
            })
            ->pluck('room_id');

        // Chambres disponibles (hors maintenance, hors chambres réservées, et filtrées par standing si demandé)
        $query = Room::whereNotIn('id', $bookedRoomIds)
            ->where('status', '!=', 'maintenance');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $availableRooms = $query->orderBy('price_per_night')->get();

        // Récupérer les photos de la galerie album depuis la base de données
        $galleryItems = DB::table('gallery_items')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('public.booking', compact('availableRooms', 'checkIn', 'checkOut', 'nights', 'galleryItems'));
    }


    /**
     * Enregistrer une réservation en ligne depuis le site web.
     */
      public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'check_in_date'    => 'required|date|after_or_equal:today',
            'check_out_date'   => 'required|date|after:check_in_date',
            'full_name'        => 'required|string|max:191',
            'phone'            => 'required|string|max:191',
            'email'            => 'nullable|email|max:255',
            'nationality'      => 'nullable|string|max:255',
            'provenance'       => 'nullable|string|max:255',
            'destination'      => 'nullable|string|max:255',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        // 1. Vérifier à nouveau que la chambre est toujours libre sur ces dates
        $isTaken = Reservation::where('room_id', $validated['room_id'])
            ->whereNotIn('status', ['cancelled', 'checked_out', 'completed'])
            ->where(function ($q) use ($validated) {
                $q->where('check_in_date', '<', $validated['check_out_date'])
                  ->where('check_out_date', '>', $validated['check_in_date']);
            })
            ->exists();

        if ($isTaken) {
            return back()->withInput()->with('error', 'Désolé, cette chambre vient d\'être réservée sur ces dates. Veuillez en choisir une autre.');
        }

        $room        = Room::findOrFail($validated['room_id']);
        $nights      = max(1, Carbon::parse($validated['check_in_date'])->diffInDays(Carbon::parse($validated['check_out_date'])));
        $totalAmount = (float) $room->price_per_night * $nights;

        $reservation = DB::transaction(function () use ($validated, $room, $nights, $totalAmount) {
        // 2. Retrouver ou créer la fiche client dans `customers` avec `full_name`
         // 2. Retrouver le client uniquement si le téléphone ET le nom correspondent (sinon créer une nouvelle fiche)
            $cleanPhone = preg_replace('/\s+/', '', trim($validated['phone']));
            $cleanName  = trim($validated['full_name']);

            $customer = Customer::where('full_name', $cleanName)
                ->where(function ($q) use ($validated, $cleanPhone) {
                    $q->where('phone', $validated['phone'])
                      ->orWhereRaw("REPLACE(phone, ' ', '') = ?", [$cleanPhone]);
                })
                ->first();

            if (!$customer) {
                $customerId = DB::table('customers')->insertGetId([
                    'full_name'   => $cleanName,
                    'phone'       => trim($validated['phone']),
                    'email'       => $validated['email'] ?? null,
                    'nationality' => $validated['nationality'] ?? 'Ivoirienne',
                    'vip_level'   => 'standard',
                    'id_type'     => 'CNI',
                    'address'     => $validated['provenance'] ?? null,
                    'notes'       => 'Client inscrit via réservation en ligne (Site Web)',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            } else {
                $customerId = $customer->id;
            }


            // 3. Créer la réservation dans `reservations`
            return Reservation::create([
                'customer_id'      => $customerId,
                'room_id'          => $room->id,
                'check_in_date'    => $validated['check_in_date'],
                'check_out_date'   => $validated['check_out_date'],
                'nights'           => $nights,
                'total_amount'     => $totalAmount,
                'deposit_paid'     => 0.00,
                'status'           => 'confirmed',
                'provenance'       => $validated['provenance'] ?? null,
                'destination'      => $validated['destination'] ?? null,
                'special_requests' => $validated['special_requests'] ?? 'Réservation effectuée en ligne (Site Web)',
            ]);
        });

        return redirect()->route('public.booking.index')
            ->with('booking_confirmed', [
                'reference' => 'RES-' . str_pad($reservation->id, 5, '0', STR_PAD_LEFT),
                'client'    => $validated['full_name'],
                'room'      => $room->number . ' (' . strtoupper($room->type) . ')',
                'check_in'  => Carbon::parse($validated['check_in_date'])->format('d/m/Y'),
                'check_out' => Carbon::parse($validated['check_out_date'])->format('d/m/Y'),
                'nights'    => $nights,
                'total'     => number_format($totalAmount, 0, ',', ' ') . ' F CFA',
            ]);
    }

}
