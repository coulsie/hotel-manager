<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceCatalog;
use App\Models\ExtraService;
use App\Models\Room;
use App\Models\Billing;

class ExtraServiceController extends Controller
{
    public function index()
    {
        $catalogs = ServiceCatalog::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $occupiedRooms = Room::where('status', 'occupied')->get();
        $services = ExtraService::with('room')->latest()->get();

        return view('services.index', compact('catalogs', 'occupiedRooms', 'services'));
    }

    // =========================================================================
    // GESTION DU CATALOGUE (GRILLE TARIFAIRE PRESSING & LAVAGE AUTO)
    // =========================================================================

    public function storeCatalog(Request $request)
    {
        $request->validate([
            'category' => 'required|in:pressing,car_wash',
            'name'     => 'required|string|max:191',
            'price'    => 'required|numeric|min:0',
        ]);

        ServiceCatalog::create([
            'category'  => $request->category,
            'name'      => $request->name,
            'price'     => $request->price,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', "Prestation « {$request->name} » ajoutée à la grille tarifaire.");
    }

    public function updateCatalog(Request $request, ServiceCatalog $catalog)
    {
        $request->validate([
            'category' => 'required|in:pressing,car_wash',
            'name'     => 'required|string|max:191',
            'price'    => 'required|numeric|min:0',
        ]);

        $catalog->update([
            'category' => $request->category,
            'name'     => $request->name,
            'price'    => $request->price,
        ]);

        return redirect()->back()->with('success', "Tarif mis à jour pour « {$catalog->name} ».");
    }

    public function destroyCatalog(ServiceCatalog $catalog)
    {
        // Désactivation douce pour préserver l'historique des anciennes factures
        $catalog->update(['is_active' => false]);

        return redirect()->back()->with('success', "La prestation « {$catalog->name} » a été retirée du catalogue actif.");
    }

    // =========================================================================
    // GESTION DES COMMANDES & PRESTATIONS (EXTÉRIEURS & CHAMBRES)
    // =========================================================================

    public function store(Request $request)
    {
        $request->validate([
            'catalog_id'     => 'required|exists:service_catalogs,id',
            'quantity'       => 'required|integer|min:1',
            'client_type'    => 'required|in:external,room',
            'room_id'        => 'required_if:client_type,room|nullable|exists:rooms,id',
            'customer_name'  => 'required_if:client_type,external|nullable|string|max:191',
            'pay_now'        => 'nullable|boolean',
            'payment_method' => 'nullable|string',
        ]);

        $item = ServiceCatalog::findOrFail($request->catalog_id);
        $qty = (int) $request->quantity;
        $total = $item->price * $qty;

        $isRoom = ($request->client_type === 'room');
        $payNow = (!$isRoom && $request->boolean('pay_now'));

        ExtraService::create([
            'category'       => $item->category,
            'service_name'   => $item->name,
            'quantity'       => $qty,
            'unit_price'     => $item->price,
            'total_amount'   => $total,
            'room_id'        => $isRoom ? $request->room_id : null,
            'customer_name'  => $request->customer_name,
            'work_status'    => 'pending',
            'payment_status' => $payNow ? 'paid' : 'unpaid',
        ]);

        if ($payNow) {
            $billing = Billing::create([
                'receipt_number' => Billing::generateReceiptNumber(),
                'room_id'        => null,
                'service_type'   => $item->category,
                'amount_paid'    => $total,
                'payment_method' => $request->input('payment_method', 'cash'),
                'status'         => 'completed',
            ]);

            return redirect()->back()->with('success', "Prestation enregistrée et encaissée (Reçu n° {$billing->receipt_number}).");
        }

        return redirect()->back()->with('success', "Prestation enregistrée ! " . ($isRoom ? "Montant ajouté à la note de chambre." : "Règlement prévu au retrait."));
    }

    public function markReady(ExtraService $service)
    {
        $service->update(['work_status' => 'ready']);
        return redirect()->back()->with('success', "Prestation marquée comme prête.");
    }

    public function payExternal(Request $request, ExtraService $service)
    {
        if ($service->payment_status === 'paid') {
            return redirect()->back()->with('error', "Cette prestation est déjà soldée.");
        }

        $billing = Billing::create([
            'receipt_number' => Billing::generateReceiptNumber(),
            'room_id'        => $service->room_id,
            'service_type'   => $service->category,
            'amount_paid'    => $service->total_amount,
            'payment_method' => $request->input('payment_method', 'cash'),
            'status'         => 'completed',
        ]);

        $service->update(['payment_status' => 'paid']);

        return redirect()->back()->with('success', "Encaissement validé ! Reçu émis : n° {$billing->receipt_number}");
    }
}
