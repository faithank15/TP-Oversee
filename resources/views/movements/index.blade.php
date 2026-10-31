@extends('layouts.app')
@section('title', 'Mouvements de stock')

@section('content')
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold m-0">Mouvements de stock</h1>
            <p class="text-muted m-0 mt-1">Historique des entrées, sorties et ajustements.</p>
        </div>
        <x-button :href="route('movements.create')" variant="accent">
            <x-iconoir-plus class="w-5 h-5" />
            Nouveau mouvement
        </x-button>
    </div>

    {{-- Filters (GET, uses existing controller query params) --}}
    <form method="GET" action="{{ route('movements.index') }}" class="flex flex-wrap items-end gap-3 mb-5">
        <label class="flex flex-col gap-1">
            <span class="text-xs font-bold uppercase tracking-wider text-muted">Produit</span>
            <select name="product"
                class="py-2.5 px-3 bg-card rounded-(--radius) border-[1.5px] border-border hover:border-primary focus:outline-none focus:border-accent focus:ring-[3px] focus:ring-accent/45">
                <option value="">Tous les produits</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected((string) request('product') === (string) $product->id)>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-xs font-bold uppercase tracking-wider text-muted">Type</span>
            <select name="type"
                class="py-2.5 px-3 bg-card rounded-(--radius) border-[1.5px] border-border hover:border-primary focus:outline-none focus:border-accent focus:ring-[3px] focus:ring-accent/45">
                <option value="">Tous les types</option>
                @foreach ($types as $type)
                    <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-xs font-bold uppercase tracking-wider text-muted">Du</span>
            <input type="date" name="from" value="{{ request('from') }}"
                class="py-2.5 px-3 bg-card rounded-(--radius) border-[1.5px] border-border hover:border-primary focus:outline-none focus:border-accent focus:ring-[3px] focus:ring-accent/45">
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-xs font-bold uppercase tracking-wider text-muted">Au</span>
            <input type="date" name="to" value="{{ request('to') }}"
                class="py-2.5 px-3 bg-card rounded-(--radius) border-[1.5px] border-border hover:border-primary focus:outline-none focus:border-accent focus:ring-[3px] focus:ring-accent/45">
        </label>

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
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Produit</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3 text-right">Quantité</th>
                        <th class="px-4 py-3 text-right">Stock</th>
                        <th class="px-4 py-3">Note</th>
                        <th class="px-4 py-3">Par</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                        @php
                            $typeVariant = match ($movement->type) {
                                \App\Enums\MovementType::Entree => 'accent',
                                \App\Enums\MovementType::Sortie => 'danger',
                                default => 'warning',
                            };
                            $delta = $movement->stock_after - $movement->stock_before;
                            $sign = $delta > 0 ? '+' : ($delta < 0 ? '−' : '');
                        @endphp
                        <tr class="border-t border-border hover:bg-accent-soft/40 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-muted">
                                {{ $movement->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($movement->product->trashed())
                                    <span class="font-semibold text-ink">{{ $movement->product->name }}</span>
                                    <span class="text-xs text-muted">(supprimé)</span>
                                @else
                                    <a href="{{ route('products.show', $movement->product) }}"
                                        class="font-semibold text-ink no-underline hover:text-primary">
                                        {{ $movement->product->name }}
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <x-badge :variant="$typeVariant">{{ $movement->type->label() }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold whitespace-nowrap tabular-nums">
                                {{ $sign }}{{ $movement->quantity }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap tabular-nums text-muted">
                                {{ $movement->stock_before }} <span class="text-muted">→</span>
                                <span class="text-ink font-semibold">{{ $movement->stock_after }}</span>
                            </td>
                            <td class="px-4 py-3 max-w-xs truncate text-muted" title="{{ $movement->note }}">
                                {{ $movement->note ?: '—' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-muted">{{ $movement->user->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <x-iconoir-data-transfer-both class="w-12 h-12 mx-auto text-muted mb-3" />
                                <p class="font-semibold text-ink m-0">Aucun mouvement</p>
                                <p class="text-muted mt-1 mb-4">Les entrées et sorties de stock apparaîtront ici.</p>
                                <x-button :href="route('movements.create')" variant="accent">
                                    <x-iconoir-plus class="w-5 h-5" />
                                    Nouveau mouvement
                                </x-button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($movements->total() > 0)
            <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-border">
                <span class="text-sm text-muted">
                    {{ $movements->firstItem() }}–{{ $movements->lastItem() }} sur {{ $movements->total() }} mouvements
                </span>
                {{ $movements->links() }}
            </div>
        @endif
    </div>
@endsection
