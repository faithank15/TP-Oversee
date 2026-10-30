<?php

namespace App\Http\Controllers;

use App\Enums\MovementType;
use App\Http\Requests\StoreMovementRequest;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $movements = StockMovement::ownedBy($user)
            ->with(['product', 'user'])
            ->when($request->integer('product'), fn ($query, $productId) => $query->where('product_id', $productId))
            ->ofType($request->query('type'))
            ->between($request->query('from'), $request->query('to'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $products = $user->products()->withTrashed()->orderBy('name')->get();
        $types = MovementType::cases();

        return view('movements.index', compact('movements', 'products', 'types'));
    }

    public function create(Request $request)
    {
        $products = $request->user()->products()->orderBy('name')->get();
        $types = MovementType::cases();
        $selectedProductId = $request->integer('product');

        return view('movements.create', compact('products', 'types', 'selectedProductId'));
    }

    public function store(StoreMovementRequest $request, StockService $stock)
    {
        // Cherché parmi les produits du compte : l'id d'une autre boutique donne une 404
        $product = $request->user()->products()->findOrFail($request->integer('product_id'));

        $stock->record(
            $product,
            $request->enum('type', MovementType::class),
            $request->integer('quantity'),
            $request->input('note'),
        );

        return redirect()->route('products.show', $product)
            ->with('success', 'Mouvement enregistré. Nouveau stock : '.$product->fresh()->quantity.'.');
    }
}
