<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category');
        $onlyLow  = $request->boolean('low_stock');

        $query = DB::table('stock_items')->orderBy('category')->orderBy('name');

        if (!empty($category)) {
            $query->where('category', $category);
        }

        if ($onlyLow) {
            $query->whereColumn('current_stock', '<=', 'alert_threshold');
        }

        $items = $query->get();

        // Statistiques globales de l'économat
        $allItems       = DB::table('stock_items')->get();
        $totalItems     = $allItems->count();
        $lowStockCount  = $allItems->filter(fn($i) => $i->current_stock <= $i->alert_threshold)->count();
        $totalValuation = $allItems->sum(fn($i) => $i->current_stock * $i->unit_cost);

        // 25 derniers mouvements de stock
        $movements = DB::table('stock_movements')
            ->join('stock_items', 'stock_movements.stock_item_id', '=', 'stock_items.id')
            ->leftJoin('users', 'stock_movements.user_id', '=', 'users.id')
            ->select(
                'stock_movements.*',
                'stock_items.name as item_name',
                'stock_items.unit as item_unit',
                'users.name as author_name'
            )
            ->orderByDesc('stock_movements.created_at')
            ->limit(25)
            ->get();

        return view('stock.index', compact(
            'items',
            'allItems',
            'movements',
            'category',
            'onlyLow',
            'totalItems',
            'lowStockCount',
            'totalValuation'
        ));
    }

    /**
     * Créer un nouvel article dans le catalogue de l'économat
     */
    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'category'        => 'required|string|max:50',
            'name'            => 'required|string|max:191',
            'unit'            => 'required|string|max:50',
            'current_stock'   => 'required|numeric|min:0',
            'alert_threshold' => 'required|numeric|min:0',
            'unit_cost'       => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $itemId = DB::table('stock_items')->insertGetId([
                'category'        => $validated['category'],
                'name'            => $validated['name'],
                'unit'            => $validated['unit'],
                'current_stock'   => $validated['current_stock'],
                'alert_threshold' => $validated['alert_threshold'],
                'unit_cost'       => $validated['unit_cost'],
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // Si un stock initial est saisi, on enregistre un mouvement d'entrée initial
            if ($validated['current_stock'] > 0) {
                DB::table('stock_movements')->insert([
                    'stock_item_id' => $itemId,
                    'type'          => 'in',
                    'quantity'      => $validated['current_stock'],
                    'department'    => 'Économat',
                    'reason'        => 'Stock initial à la création',
                    'user_id'       => Auth::id(),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        });

        return back()->with('success', "Article '{$validated['name']}' ajouté à l'économat.");
    }

    /**
     * Enregistrer une Entrée (Approvisionnement) ou une Sortie / Perte
     */
    public function storeMovement(Request $request)
    {
        $validated = $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'type'          => 'required|in:in,out,loss',
            'quantity'      => 'required|numeric|min:0.01',
            'department'    => 'nullable|string|max:100',
            'reason'        => 'nullable|string|max:255',
        ]);

        $item = DB::table('stock_items')->where('id', $validated['stock_item_id'])->first();

        // Vérification : empêcher une sortie supérieure au stock disponible
        if (in_array($validated['type'], ['out', 'loss']) && $validated['quantity'] > $item->current_stock) {
            return back()->with('error', "Stock insuffisant pour '{$item->name}' (Disponible : {$item->current_stock} {$item->unit}).");
        }

        DB::transaction(function () use ($validated, $item) {
            $newStock = ($validated['type'] === 'in')
                ? $item->current_stock + $validated['quantity']
                : $item->current_stock - $validated['quantity'];

            DB::table('stock_items')
                ->where('id', $item->id)
                ->update([
                    'current_stock' => $newStock,
                    'updated_at'    => now(),
                ]);

            DB::table('stock_movements')->insert([
                'stock_item_id' => $item->id,
                'type'          => $validated['type'],
                'quantity'      => $validated['quantity'],
                'department'    => $validated['department'] ?? 'Général',
                'reason'        => $validated['reason'] ?? null,
                'user_id'       => Auth::id(),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        });

        $label = $validated['type'] === 'in' ? 'Entrée en stock' : ($validated['type'] === 'out' ? 'Sortie de stock' : 'Perte / Casse');
        return back()->with('success', "{$label} enregistrée pour '{$item->name}'.");
    }
}
