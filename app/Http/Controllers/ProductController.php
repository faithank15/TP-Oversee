<?php

namespace App\Http\Controllers;

use App\Enums\MovementType;
use App\Enums\Unit;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ReferenceGenerator;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sorts = [
            'name' => 'name',
            'quantity' => 'quantity',
            'price' => 'price',
            'value' => DB::raw('quantity * price'),
        ];
        $sort = $sorts[$request->query('sort')] ?? 'name';
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $products = $request->user()->products()
            ->with('category')
            ->search($request->query('q'))
            ->inCategory($request->query('category'))
            ->stockStatus($request->query('status'))
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        $categories = $request->user()->categories()->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create(Request $request)
    {
        $categories = $request->user()->categories()->orderBy('name')->get();
        $units = Unit::cases();

        return view('products.create', compact('categories', 'units'));
    }

    public function store(StoreProductRequest $request, StockService $stock, ReferenceGenerator $refs)
    {
        $product = DB::transaction(function () use ($request, $stock, $refs) {
            $product = $request->user()->products()->create([
                ...$request->safe()->except('initial_quantity'),
                'reference' => $request->validated('reference') ?? $refs->placeholder(),
            ]);
            $refs->assignIfMissing($product);

            if ($request->integer('initial_quantity') > 0) {
                $stock->record($product, MovementType::Entree, $request->integer('initial_quantity'), 'Stock initial');
            }

            return $product;
        });

        return redirect()->route('products.show', $product)
            ->with('success', "Produit « {$product->name} » ajouté.");
    }

    public function show(Product $product)
    {
        Gate::authorize('view', $product);

        $product->load('category');
        $movements = $product->movements()->with('user')->paginate(15);

        return view('products.show', compact('product', 'movements'));
    }

    public function edit(Request $request, Product $product)
    {
        Gate::authorize('view', $product);

        $categories = $request->user()->categories()->orderBy('name')->get();
        $units = Unit::cases();

        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        Gate::authorize('update', $product);

        $product->update([
            ...$request->validated(),
            'reference' => $request->validated('reference') ?? $product->reference,
        ]);

        return redirect()->route('products.show', $product)->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product)
    {
        Gate::authorize('delete', $product);

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', "Produit « {$product->name} » supprimé. Son historique est conservé.");
    }

    public function trash(Request $request)
    {
        $products = $request->user()->products()->onlyTrashed()->with('category')->latest('deleted_at')->paginate(10);

        return view('products.trash', compact('products'));
    }

    public function restore(Request $request, int $id)
    {
        $product = $request->user()->products()->onlyTrashed()->findOrFail($id);

        Gate::authorize('restore', $product);

        $product->restore();

        return redirect()->route('products.show', $product)
            ->with('success', "Produit « {$product->name} » restauré.");
    }
}
