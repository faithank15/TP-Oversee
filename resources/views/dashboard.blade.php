@extends('layouts.app')
@section('title', 'Tableau de bord')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold m-0">Tableau de bord</h1>
        <p class="text-muted m-0 mt-1">Vue d'ensemble de votre inventaire.</p>
    </div>

    {{-- Stat tiles --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('products.index') }}" class="bg-card rounded-(--radius) p-5 no-underline block hover:shadow-card transition">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-muted m-0">Produits</p>
                <x-iconoir-box-iso class="w-5 h-5 text-muted" />
            </div>
            <p class="text-3xl font-bold m-0 mt-2 tabular-nums text-ink">{{ $productsCount }}</p>
        </a>

        <div class="bg-card rounded-(--radius) p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-muted m-0">Valeur du stock</p>
                <x-iconoir-coins class="w-5 h-5 text-muted" />
            </div>
            <p class="text-3xl font-bold m-0 mt-2 tabular-nums text-ink">
                {{ number_format($stockValue, 0, ',', ' ') }} <span class="text-base font-normal text-muted">FCFA</span>
            </p>
        </div>

        <a href="{{ route('products.index', ['status' => 'low']) }}" class="bg-card rounded-(--radius) p-5 no-underline block hover:shadow-card transition">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-muted m-0">Stock faible</p>
                <x-iconoir-warning-triangle class="w-5 h-5 text-warning" />
            </div>
            <p class="text-3xl font-bold m-0 mt-2 tabular-nums text-warning">{{ $lowStockCount }}</p>
        </a>

        <a href="{{ route('products.index', ['status' => 'out']) }}" class="bg-card rounded-(--radius) p-5 no-underline block hover:shadow-card transition">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-muted m-0">Rupture</p>
                <x-iconoir-xmark-circle class="w-5 h-5 text-danger" />
            </div>
            <p class="text-3xl font-bold m-0 mt-2 tabular-nums text-danger">{{ $outOfStockCount }}</p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Stock alerts --}}
        <div class="lg:col-span-2 bg-card rounded-(--radius) overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-border">
                <h3 class="text-lg font-bold m-0 flex items-center gap-2">
                    <x-iconoir-bell class="w-5 h-5 text-warning" />
                    Alertes de stock
                </h3>
                <a href="{{ route('products.index', ['status' => 'low']) }}"
                    class="text-sm font-semibold text-primary no-underline hover:underline">Tout voir</a>
            </div>

            @forelse ($alerts as $product)
                <a href="{{ route('products.show', $product) }}"
                    class="flex items-center justify-between gap-3 px-5 py-3 border-b border-border last:border-0 no-underline hover:bg-accent-soft/40 transition">
                    <div class="min-w-0">
                        <span class="font-semibold text-ink block truncate">{{ $product->name }}</span>
                        <span class="text-xs text-muted">{{ $product->category->name }}</span>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="font-semibold tabular-nums {{ $product->is_out_of_stock ? 'text-danger' : 'text-warning' }}">
                            {{ $product->quantity }} {{ $product->unit->label() }}
                        </span>
                        <span class="block text-xs text-muted">seuil : {{ $product->alert_threshold }}</span>
                    </div>
                </a>
            @empty
                <div class="px-5 py-10 text-center text-muted">
                    <x-iconoir-warning-circle class="w-10 h-10 mx-auto mb-2 text-muted" />
                    Aucune alerte : tous vos stocks sont au-dessus du seuil.
                </div>
            @endforelse
        </div>

        {{-- 30-day flows --}}
        <div class="bg-card rounded-(--radius) p-5">
            <h3 class="text-lg font-bold m-0 mb-4">Flux sur 30 jours</h3>

            <div class="flex items-center gap-3 mb-4">
                <span class="w-10 h-10 rounded-(--radius-sm) bg-accent-soft text-accent-hover grid place-content-center shrink-0">
                    <x-iconoir-graph-up class="w-5 h-5" />
                </span>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-muted m-0">Entrées</p>
                    <p class="text-xl font-bold m-0 tabular-nums">{{ number_format($entriesLast30Days, 0, ',', ' ') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-(--radius-sm) bg-danger-soft text-danger grid place-content-center shrink-0">
                    <x-iconoir-graph-down class="w-5 h-5" />
                </span>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-muted m-0">Sorties</p>
                    <p class="text-xl font-bold m-0 tabular-nums">{{ number_format($exitsLast30Days, 0, ',', ' ') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Latest movements --}}
        <div class="lg:col-span-2 bg-card rounded-(--radius) overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-border">
                <h3 class="text-lg font-bold m-0">Derniers mouvements</h3>
                <a href="{{ route('movements.index') }}"
                    class="text-sm font-semibold text-primary no-underline hover:underline">Tout voir</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <tbody>
                        @forelse ($latestMovements as $movement)
                            @php
                                $typeVariant = match ($movement->type) {
                                    \App\Enums\MovementType::Entree => 'accent',
                                    \App\Enums\MovementType::Sortie => 'danger',
                                    default => 'warning',
                                };
                                $delta = $movement->stock_after - $movement->stock_before;
                                $sign = $delta > 0 ? '+' : ($delta < 0 ? '−' : '');
                            @endphp
                            <tr class="border-b border-border last:border-0 hover:bg-accent-soft/40 transition">
                                <td class="px-5 py-3 whitespace-nowrap text-muted">{{ $movement->created_at->format('d/m H:i') }}</td>
                                <td class="px-5 py-3 font-semibold text-ink truncate max-w-48">{{ $movement->product->name }}</td>
                                <td class="px-5 py-3"><x-badge :variant="$typeVariant">{{ $movement->type->label() }}</x-badge></td>
                                <td class="px-5 py-3 text-right font-semibold whitespace-nowrap tabular-nums">{{ $sign }}{{ $movement->quantity }}</td>
                            </tr>
                        @empty
                            <tr><td class="px-5 py-10 text-center text-muted">Aucun mouvement récent.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Value by category --}}
        <div class="bg-card rounded-(--radius) p-5">
            <h3 class="text-lg font-bold m-0 mb-4">Valeur par catégorie</h3>

            @php $maxValue = $valueByCategory->max('stock_value') ?: 1; @endphp

            @forelse ($valueByCategory as $category)
                <div class="mb-3 last:mb-0">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="text-sm font-semibold text-ink truncate">{{ $category->name }}</span>
                        <span class="text-sm text-muted tabular-nums whitespace-nowrap">{{ number_format($category->stock_value, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="h-2 bg-background rounded-pill overflow-hidden">
                        <div class="h-full bg-accent rounded-pill" style="width: {{ round(($category->stock_value / $maxValue) * 100) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-muted m-0">Aucune catégorie.</p>
            @endforelse
        </div>
    </div>
@endsection
