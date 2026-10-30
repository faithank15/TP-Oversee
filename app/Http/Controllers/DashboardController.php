<?php

namespace App\Http\Controllers;

use App\Enums\MovementType;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $productsCount = $user->products()->count();
        $stockValue = (int) $user->products()->sum(DB::raw('quantity * price'));
        $lowStockCount = $user->products()->lowStock()->where('quantity', '>', 0)->count();
        $outOfStockCount = $user->products()->where('quantity', 0)->count();

        $alerts = $user->products()->with('category')->lowStock()->orderBy('quantity')->limit(5)->get();

        $latestMovements = StockMovement::ownedBy($user)->with(['product', 'user'])->latest()->limit(5)->get();

        $valueByCategory = $user->categories()
            ->leftJoin('products', fn ($join) => $join
                ->on('products.category_id', '=', 'categories.id')
                ->whereNull('products.deleted_at'))
            ->selectRaw('categories.id, categories.name, COALESCE(SUM(products.quantity * products.price), 0) as stock_value')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('categories.name')
            ->get();

        $flows = StockMovement::ownedBy($user)
            ->where('created_at', '>=', now()->subDays(30))
            ->whereIn('type', [MovementType::Entree, MovementType::Sortie])
            ->selectRaw('type, SUM(ABS(quantity)) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $entriesLast30Days = (int) ($flows[MovementType::Entree->value] ?? 0);
        $exitsLast30Days = (int) ($flows[MovementType::Sortie->value] ?? 0);

        return view('dashboard', compact(
            'productsCount',
            'stockValue',
            'lowStockCount',
            'outOfStockCount',
            'alerts',
            'latestMovements',
            'valueByCategory',
            'entriesLast30Days',
            'exitsLast30Days',
        ));
    }
}
