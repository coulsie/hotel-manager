<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CrmController extends Controller
{
        public function index(Request $request)
    {
        $search    = $request->input('search');
        $vipFilter = $request->input('vip_level');
        $companyId = $request->input('company_id');

        // 1. Liste des entreprises partenaires
        $companies = DB::table('companies')->orderBy('name')->get();

        // 2. Récupération dynamique de toutes les colonnes de customers pour éviter tout conflit SQL
        $customerColumns = array_map(
            fn($col) => "customers.{$col}",
            \Illuminate\Support\Facades\Schema::getColumnListing('customers')
        );

        // 3. Requête des clients avec statistiques de séjours
        $query = DB::table('customers')
            ->leftJoin('companies', 'customers.company_id', '=', 'companies.id')
            ->leftJoin('reservations', function ($join) {
                $join->on('customers.id', '=', 'reservations.customer_id')
                     ->whereIn('reservations.status', ['confirmed', 'checked_in', 'completed']);
            })
            ->select(
                'customers.*',
                'companies.name as company_name',
                DB::raw('COUNT(reservations.id) as total_stays'),
                DB::raw('COALESCE(SUM(reservations.nights), 0) as total_nights'),
                DB::raw('COALESCE(SUM(reservations.total_amount), 0) as total_spent')
            )
            ->groupBy(array_merge($customerColumns, ['companies.name']));

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('customers.full_name', 'LIKE', "%{$search}%")
                  ->orWhere('customers.phone', 'LIKE', "%{$search}%")
                  ->orWhere('customers.id_card_number', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($vipFilter)) {
            $query->where('customers.vip_level', $vipFilter);
        }

        if (!empty($companyId)) {
            $query->where('customers.company_id', $companyId);
        }

        $customers = $query->orderByDesc('total_spent')->get();

        // 4. Indicateurs globaux CRM
        $totalCustomers = DB::table('customers')->count();
        $vipCount       = DB::table('customers')->where('vip_level', 'vip')->count();
        $companiesCount = $companies->count();

        return view('crm.index', compact(
            'customers',
            'companies',
            'search',
            'vipFilter',
            'companyId',
            'totalCustomers',
            'vipCount',
            'companiesCount'
        ));
    }

    /**
     * Enregistrer une nouvelle Entreprise / Société Partenaire
     */
    public function storeCompany(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:191',
            'tax_id'              => 'nullable|string|max:100',
            'phone'               => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:191',
            'address'             => 'nullable|string|max:255',
            'negotiated_discount' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::table('companies')->insert([
            'name'                => strtoupper($validated['name']),
            'tax_id'              => $validated['tax_id'] ?? null,
            'phone'               => $validated['phone'] ?? null,
            'email'               => $validated['email'] ?? null,
            'address'             => $validated['address'] ?? null,
            'negotiated_discount' => $validated['negotiated_discount'] ?? 0,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        return back()->with('success', "Société partenaire '{$validated['name']}' enregistrée.");
    }

    /**
     * Mettre à jour la fiche CRM d'un client (Statut VIP, Société, Préférences)
     */
    public function updateCustomer(Request $request, int $id)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:191',
            'phone'          => 'required|string|max:50',
            'email'          => 'nullable|email|max:191',
            'id_card_number' => 'nullable|string|max:100',
            'nationality'    => 'nullable|string|max:100',
            'company_id'     => 'nullable|exists:companies,id',
            'vip_level'      => 'required|in:standard,regular,vip',
            'preferences'    => 'nullable|string|max:1000',
        ]);

        DB::table('customers')->where('id', $id)->update([
            'full_name'      => $validated['full_name'],
            'phone'          => $validated['phone'],
            'email'          => $validated['email'] ?? null,
            'id_card_number' => $validated['id_card_number'] ?? null,
            'nationality'    => $validated['nationality'] ?? null,
            'company_id'     => $validated['company_id'] ?: null,
            'vip_level'      => $validated['vip_level'],
            'preferences'    => $validated['preferences'] ?? null,
            'updated_at'     => now(),
        ]);

        return back()->with('success', "Fiche client mise à jour pour {$validated['full_name']}.");
    }

    /**
     * État de Facturation Consolidé d'une Entreprise sur une période
     */
    public function companyStatement(Request $request, int $id)
    {
        $company = DB::table('companies')->where('id', $id)->firstOrFail();

        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('date_to', now()->endOfMonth()->toDateString());

        $stays = Reservation::with(['customer', 'room'])
            ->whereHas('customer', function ($q) use ($id) {
                $q->where('company_id', $id);
            })
            ->whereIn('status', ['confirmed', 'checked_in', 'completed'])
            ->whereDate('check_in_date', '>=', $dateFrom)
            ->whereDate('check_in_date', '<=', $dateTo)
            ->orderBy('check_in_date')
            ->get();

        $grossTotal = $stays->sum('total_amount');
        $discountAmount = round(($grossTotal * $company->negotiated_discount) / 100);
        $netTotal = $grossTotal - $discountAmount;
        $totalDeposits = $stays->sum('deposit_paid');
        $balanceDue = max(0, $netTotal - $totalDeposits);

        return view('crm.company_statement', compact(
            'company',
            'stays',
            'dateFrom',
            'dateTo',
            'grossTotal',
            'discountAmount',
            'netTotal',
            'totalDeposits',
            'balanceDue'
        ));
    }
}
