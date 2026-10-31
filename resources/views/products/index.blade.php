@extends('layouts.app')
@section('title', 'Produits')

@section('content')
    @php
        $sort = request('sort', 'name');
        $dir = request('dir', 'asc');
        // Build a sort URL for a column, toggling direction, keeping other filters.
        $sortUrl = fn(string $column) => request()->fullUrlWithQuery([
            'sort' => $column,
            'dir' => $sort === $column && $dir === 'asc' ? 'desc' : 'asc',
            'page' => null,
        ]);
    @endphp

    {{-- Page header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold m-0">Produits</h1>
            <p class="text-muted m-0 mt-1">Gérez vos articles, leurs stocks et leurs valeurs.</p>
        </div>
        <x-button :href="route('products.create')" variant="accent">
            <x-iconoir-plus class="w-5 h-5" />
            Nouveau produit
        </x-button>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-6 border-b border-border mb-5">
        <a href="{{ route('products.index') }}" @class([
            'pb-3 -mb-px font-semibold border-b-2 transition',
            'border-accent text-ink' => true,
        ])>Actifs</a>
        <a href="{{ route('products.trash') }}"
            class="pb-3 -mb-px font-semibold border-b-2 border-transparent text-muted hover:text-ink transition">
            Corbeille
        </a>
    </div>

    {{-- Toolbar : search + filters (GET, uses existing controller query params) --}}
    <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap items-center gap-3 mb-5">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="dir" value="{{ $dir }}">

        <div class="relative grow min-w-56">
            <x-iconoir-search class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-muted" />
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher par nom ou référence…"
                class="w-full pl-10 pr-3 py-2.5 bg-card rounded-(--radius) border-[1.5px] border-border transition placeholder:text-muted/80 hover:border-primary focus:outline-none focus:border-accent focus:ring-[3px] focus:ring-accent/45">
        </div>

        <select name="category"
            class="py-2.5 px-3 bg-card rounded-(--radius) border-[1.5px] border-border hover:border-primary focus:outline-none focus:border-accent focus:ring-[3px] focus:ring-accent/45">
            <option value="">Toutes les catégories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="status"
            class="py-2.5 px-3 bg-card rounded-(--radius) border-[1.5px] border-border hover:border-primary focus:outline-none focus:border-accent focus:ring-[3px] focus:ring-accent/45">
            <option value="">Tous les statuts</option>
            <option value="ok" @selected(request('status') === 'ok')>En stock</option>
            <option value="low" @selected(request('status') === 'low')>Stock faible</option>
            <option value="out" @selected(request('status') === 'out')>Rupture</option>
        </select>

        <x-button type="submit" variant="outline">
            <x-iconoir-filter-list class="w-5 h-5" />
            Filtrer
        </x-button>
    </form>

    {{-- Table --}}
    <div class="bg-card rounded-(--radius) overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-muted">
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" class="w-4 h-4 rounded-sm accent-accent align-middle">
                        </th>
                        <th class="px-4 py-3">
                            <a href="{{ $sortUrl('name') }}" class="inline-flex items-center gap-1 hover:text-ink">
                                Produit
                                @svg('iconoir-nav-arrow-' . ($dir === 'asc' ? 'up' : 'down'), 'w-4 h-4' . ($sort === 'name' ? '' : ' invisible'))
                            </a>
                        </th>
                        <th class="px-4 py-3">Catégorie</th>
                        <th class="px-4 py-3">Unité</th>
                        <th class="px-4 py-3">
                            <a href="{{ $sortUrl('price') }}" class="inline-flex items-center gap-1 hover:text-ink">
                                Prix
                                @svg('iconoir-nav-arrow-' . ($dir === 'asc' ? 'up' : 'down'), 'w-4 h-4' . ($sort === 'price' ? '' : ' invisible'))
                            </a>
                        </th>
                        <th class="px-4 py-3">
                            <a href="{{ $sortUrl('quantity') }}" class="inline-flex items-center gap-1 hover:text-ink">
                                Quantité
                                @svg('iconoir-nav-arrow-' . ($dir === 'asc' ? 'up' : 'down'), 'w-4 h-4' . ($sort === 'quantity' ? '' : ' invisible'))
                            </a>
                        </th>
                        <th class="px-4 py-3">
                            <a href="{{ $sortUrl('value') }}" class="inline-flex items-center gap-1 hover:text-ink">
                                Valeur
                                @svg('iconoir-nav-arrow-' . ($dir === 'asc' ? 'up' : 'down'), 'w-4 h-4' . ($sort === 'value' ? '' : ' invisible'))
                            </a>
                        </th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $status = $product->is_out_of_stock
                                ? ['Rupture', 'danger']
                                : ($product->is_low_stock
                                    ? ['Stock faible', 'warning']
                                    : ['En stock', 'accent']);
                        @endphp
                        <tr class="border-t border-border hover:bg-accent-soft/40 transition">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="w-4 h-4 rounded-sm accent-accent align-middle">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="min-w-0">
                                        <a href="{{ route('products.show', $product) }}"
                                            class="font-semibold text-ink no-underline hover:text-primary block truncate">
                                            {{ $product->name }}
                                        </a>
                                        <span class="text-xs text-muted">{{ $product->reference }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <x-badge>{{ $product->category->name }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $product->unit->label() }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ number_format($product->price, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-4 py-3 font-semibold whitespace-nowrap">
                                {{ $product->quantity }}
                                <span class="text-muted font-normal">{{ $product->unit->label() }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ number_format($product->stock_value, 0, ',', ' ') }}
                                FCFA</td>
                            <td class="px-4 py-3">
                                <x-badge :variant="$status[1]">{{ $status[0] }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <x-dropdown>
                                    <x-slot:trigger>
                                        <x-iconoir-more-horiz class="w-5 h-5" />
                                    </x-slot:trigger>

                                    <x-dropdown.item :href="route('products.show', $product)" icon="eye">
                                        Voir
                                    </x-dropdown.item>
                                    <x-dropdown.item :href="route('products.edit', $product)" icon="edit">
                                        Modifier
                                    </x-dropdown.item>

                                    <x-dropdown.divider />

                                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                                        onsubmit="return confirm('Supprimer « {{ $product->name }} » ? Il ira dans la corbeille.');">
                                        @csrf
                                        @method('DELETE')
                                        <x-dropdown.item type="submit" icon="trash" variant="danger">
                                            Supprimer
                                        </x-dropdown.item>
                                    </form>
                                </x-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center">
                                <x-iconoir-box-iso class="w-12 h-12 mx-auto text-muted mb-3" />
                                <p class="font-semibold text-ink m-0">Aucun produit pour l'instant</p>
                                <p class="text-muted mt-1 mb-4">Ajoutez votre premier produit pour suivre son stock.</p>
                                <x-button :href="route('products.create')" variant="accent">
                                    <x-iconoir-plus class="w-5 h-5" />
                                    Nouveau produit
                                </x-button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer : count + pagination --}}
        @if ($products->hasPages() || $products->total() > 0)
            <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-border">
                <span class="text-sm text-muted">
                    {{ $products->firstItem() }}–{{ $products->lastItem() }} sur {{ $products->total() }} produits
                </span>
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
