<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = DB::table('hotel_activities')
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $occupiedRooms = Room::where('status', 'occupied')->orderBy('number')->get();

        $bookings = DB::table('activity_bookings')
            ->join('hotel_activities', 'activity_bookings.hotel_activity_id', '=', 'hotel_activities.id')
            ->leftJoin('rooms', 'activity_bookings.room_id', '=', 'rooms.id')
            ->select(
                'activity_bookings.*',
                'hotel_activities.name as activity_name',
                'hotel_activities.category as activity_category',
                'rooms.number as room_number'
            )
            ->orderByDesc('activity_bookings.created_at')
            ->limit(30)
            ->get();

        return view('activities.index', compact('activities', 'occupiedRooms', 'bookings'));
    }

    /**
     * Ajouter une nouvelle offre au catalogue (Spa, Piscine, Navette, Séminaire...)
     */
    public function storeCatalog(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'name'     => 'required|string|max:191',
            'price'    => 'required|numeric|min:0',
            'unit'     => 'required|string|max:50',
        ]);

        DB::table('hotel_activities')->insert([
            'category'   => $validated['category'],
            'name'       => $validated['name'],
            'price'      => $validated['price'],
            'unit'       => $validated['unit'],
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', "Prestation '{$validated['name']}' ajoutée au catalogue.");
    }

    /**
     * Enregistrer une vente ou réservation d'activité (Chambre ou Client Externe)
     */
    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'hotel_activity_id' => 'required|exists:hotel_activities,id',
            'billing_mode'      => 'required|in:direct,room', // 'direct' = paiement immédiat, 'room' = sur chambre
            'room_id'           => 'required_if:billing_mode,room|nullable|exists:rooms,id',
            'client_name'       => 'nullable|string|max:191',
            'quantity'          => 'required|integer|min:1',
            'payment_method'    => 'nullable|string|max:50',
            'notes'             => 'nullable|string|max:500',
        ]);

        $activity = DB::table('hotel_activities')->where('id', $validated['hotel_activity_id'])->first();
        $totalAmount = $activity->price * $validated['quantity'];
        $billing = null;

        DB::transaction(function () use ($validated, $activity, $totalAmount, &$billing) {
            $isDirect = ($validated['billing_mode'] === 'direct');

            DB::table('activity_bookings')->insert([
                'hotel_activity_id' => $activity->id,
                'room_id'           => $isDirect ? null : $validated['room_id'],
                'client_name'       => $validated['client_name'] ?? ($isDirect ? 'Client Externe' : null),
                'quantity'          => $validated['quantity'],
                'unit_price'        => $activity->price,
                'total_amount'      => $totalAmount,
                'payment_status'    => $isDirect ? 'paid' : 'unpaid',
                'notes'             => $validated['notes'] ?? null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // Si paiement immédiat (client externe), création directe du reçu dans le Grand Livre
            if ($isDirect) {
                $billing = Billing::create([
                    'receipt_number' => Billing::generateReceiptNumber(),
                    'room_id'        => null,
                    'service_type'   => $activity->category, // 'piscine', 'spa', 'seminaire', 'navette'...
                    'amount_paid'    => $totalAmount,
                    'payment_method' => $validated['payment_method'] ?? 'Espèces',
                    'status'         => 'completed',
                ]);
            }
        });

        if ($billing) {
            return redirect()->route('billing.payments')
                ->with('success', "Prestation encaissée : {$activity->name} (Reçu n° {$billing->receipt_number}).")
                ->with('print_billing_id', $billing->id);
        }

        return back()->with('success', "Prestation '{$activity->name}' imputée sur le compte de la chambre.");
    }

        public function updateCatalog(\Illuminate\Http\Request $request, $id)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'name'     => 'required|string|max:191',
            'price'    => 'required|numeric|min:0',
            'unit'     => 'required|string|max:50',
        ]);

        \Illuminate\Support\Facades\DB::table('hotel_activities')
            ->where('id', $id)
            ->update([
                'category'   => $validated['category'],
                'name'       => $validated['name'],
                'price'      => $validated['price'],
                'unit'       => $validated['unit'],
                'updated_at' => now(),
            ]);

        return back()->with('success', "La prestation « {$validated['name']} » a été mise à jour.");
    }

    public function destroyCatalog($id)
    {
        \Illuminate\Support\Facades\DB::table('hotel_activities')->where('id', $id)->delete();

        return back()->with('success', "L'offre a été retirée du catalogue des activités.");
    }

}
