<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::today()->toDateString());
        $dateTo   = $request->input('date_to', Carbon::today()->toDateString());
        $category = $request->input('category');

        // 1. Liste filtrée des dépenses
        $query = DB::table('expenses')
            ->leftJoin('users', 'expenses.user_id', '=', 'users.id')
            ->whereDate('expenses.expense_date', '>=', $dateFrom)
            ->whereDate('expenses.expense_date', '<=', $dateTo)
            ->select('expenses.*', 'users.name as author_name');

        if (!empty($category)) {
            $query->where('expenses.category', $category);
        }

        $expenses = $query->orderByDesc('expenses.expense_date')
            ->orderByDesc('expenses.id')
            ->get();

        // 2. Calcul du Solde Net en Caisse AUJOURD'HUI (Espèces uniquement)
        $todayCashIn = Billing::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->whereIn('payment_method', ['Espèces', 'cash', 'especes'])
            ->sum('amount_paid');

        $todayCashOut = DB::table('expenses')
            ->whereDate('expense_date', Carbon::today())
            ->whereIn('payment_method', ['Espèces', 'cash', 'especes'])
            ->sum('amount');

        $netCashDrawer = $todayCashIn - $todayCashOut;

        // 3. Totaux sur la période filtrée
        $periodTotalExpenses = $expenses->sum('amount');

        return view('expenses.index', compact(
            'expenses',
            'dateFrom',
            'dateTo',
            'category',
            'todayCashIn',
            'todayCashOut',
            'netCashDrawer',
            'periodTotalExpenses'
        ));
    }

    /**
     * Enregistrer un nouveau décaissement / bon de sortie
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date'   => 'required|date',
            'category'       => 'required|string|max:50',
            'description'    => 'required|string|max:255',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string|max:50',
            'beneficiary'    => 'nullable|string|max:191',
            'reference'      => 'nullable|string|max:100',
        ]);

        // Génération automatique d'un numéro de Bon de Sortie (ex: DEP-20261116-001)
        $datePrefix = Carbon::parse($validated['expense_date'])->format('Ymd');
        $countToday = DB::table('expenses')->whereDate('expense_date', $validated['expense_date'])->count() + 1;
        $voucherNumber = 'DEP-' . $datePrefix . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

        DB::table('expenses')->insert([
            'voucher_number' => $voucherNumber,
            'category'       => $validated['category'],
            'description'    => $validated['description'],
            'amount'         => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'beneficiary'    => $validated['beneficiary'] ?? null,
            'reference'      => $validated['reference'] ?? null,
            'user_id'        => Auth::id(),
            'expense_date'   => $validated['expense_date'],
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return back()->with('success', "Dépense enregistrée (Bon de sortie n° {$voucherNumber}).");
    }

    /**
     * Supprimer une dépense (erreur de saisie)
     */
    public function destroy(int $id)
    {
        DB::table('expenses')->where('id', $id)->delete();

        return back()->with('success', 'Bon de dépense supprimé.');
    }

    /**
     * Mettre à jour un bon de dépense existant
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'category'       => 'required|string|max:50',
            'description'    => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:50',
            'beneficiary'    => 'nullable|string|max:191',
            'reference'      => 'nullable|string|max:100',
            'expense_date'   => 'required|date',
        ]);

        DB::table('expenses')->where('id', $id)->update([
            'category'       => $validated['category'],
            'description'    => $validated['description'],
            'amount'         => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'beneficiary'    => $validated['beneficiary'] ?? null,
            'reference'      => $validated['reference'] ?? null,
            'expense_date'   => $validated['expense_date'],
            'updated_at'     => now(),
        ]);

        return back()->with('success', 'Le bon de dépense a été modifié avec succès.');
    }



}
