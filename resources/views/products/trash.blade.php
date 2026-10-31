@extends('layouts.app')
@section('title', 'Corbeille')

@section('content')
    {{-- Page header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold m-0">Corbeille</h1>
            <p class="text-muted m-0 mt-1">Produits supprimés. Leur historique de stock est conservé.</p>
        </div>
        <x-button :href="route('products.index')" variant="outline">
            <x-iconoir-nav-arrow-left class="w-5 h-5" />
            Retour aux produits
        </x-button>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-6 border-b border-border mb-5">
        <a href="{{ route('products.index') }}"
            class="pb-3 -mb-px font-semibold border-b-2 border-transparent text-muted hover:text-ink transition">
            Actifs
        </a>
        <a href="{{ route('products.trash') }}"
            class="pb-3 -mb-px font-semibold border-b-2 border-accent text-ink">
            Corbeille
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-card rounded-(--radius) overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-muted">
                        <th class="px-4 py-3">Produit</th>
                        <th class="px-4 py-3">Catégorie</th>
                        <th class="px-4 py-3 text-right">Prix</th>
                        <th class="px-4 py-3 text-right">Quantité</th>
                        <th class="px-4 py-3">Supprimé le</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="border-t border-border hover:bg-accent-soft/40 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-(--radius-sm) bg-background text-muted font-bold flex items-center justify-center shrink-0 uppercase">
                                        {{ \Illuminate\Support\Str::substr($product->name, 0, 1) }}
                                    </span>
                                    <div class="min-w-0">
                                        <span class="font-semibold text-ink block truncate">{{ $product->name }}</span>
                                        <span class="text-xs text-muted">{{ $product->reference }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <x-badge>{{ $product->category->name }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap tabular-nums">
                                {{ number_format($product->price, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap tabular-nums">
                                {{ $product->quantity }} <span class="text-muted">{{ $product->unit->label() }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-muted">
                                {{ $product->deleted_at?->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('products.restore', $product->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <x-button type="submit" variant="outline" size="sm">
                                        <x-iconoir-undo class="w-4 h-4" />
                                        Restaurer
                                    </x-button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                <x-iconoir-trash class="w-12 h-12 mx-auto text-muted mb-3" />
                                <p class="font-semibold text-ink m-0">La corbeille est vide</p>
                                <p class="text-muted mt-1">Les produits supprimés apparaîtront ici.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->total() > 0)
            <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-border">
                <span class="text-sm text-muted">
                    {{ $products->firstItem() }}–{{ $products->lastItem() }} sur {{ $products->total() }} produits supprimés
                </span>
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
