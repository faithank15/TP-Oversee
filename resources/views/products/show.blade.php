@extends('layouts.app')
@section('title', $product->name)

@section('content')
    @php
        $status = $product->is_out_of_stock
            ? ['Rupture', 'danger']
            : ($product->is_low_stock ? ['Stock faible', 'warning'] : ['En stock', 'accent']);
    @endphp

    <div class="w-full">
        <a href="{{ route('products.index') }}"
            class="inline-flex items-center gap-1 text-sm font-semibold text-muted hover:text-ink no-underline mb-4">
            <x-iconoir-nav-arrow-left class="w-4 h-4" />
            Retour aux produits
        </a>

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
            <div class="flex items-center gap-4 min-w-0">
                <span class="w-14 h-14 rounded-(--radius) bg-accent-soft text-primary font-extrabold text-2xl grid place-content-center shrink-0 uppercase">
                    {{ \Illuminate\Support\Str::substr($product->name, 0, 1) }}
                </span>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-3xl font-bold m-0 truncate">{{ $product->name }}</h1>
                        <x-badge :variant="$status[1]">{{ $status[0] }}</x-badge>
                    </div>
                    <p class="text-muted m-0 mt-1">
                        {{ $product->reference }} · {{ $product->category->name }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <x-button :href="route('products.edit', $product)" variant="outline">
                    <x-iconoir-edit class="w-5 h-5" />
                    Modifier
                </x-button>
                <x-button :href="route('movements.create', ['product' => $product->id])" variant="accent">
                    <x-iconoir-data-transfer-both class="w-5 h-5" />
                    Nouveau mouvement
                </x-button>
                <x-dropdown>
                    <x-slot:trigger>
                        <x-iconoir-more-horiz class="w-5 h-5" />
                    </x-slot:trigger>
                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                        onsubmit="return confirm('Supprimer « {{ $product->name }} » ? Il ira dans la corbeille.');">
                        @csrf
                        @method('DELETE')
                        <x-dropdown.item type="submit" icon="trash" variant="danger">Supprimer</x-dropdown.item>
                    </form>
                </x-dropdown>
            </div>
        </div>

        {{-- Low stock alert --}}
        @if ($product->is_out_of_stock)
            <x-alert variant="danger" class="mb-6 flex items-center gap-2">
                <x-iconoir-warning-triangle class="w-5 h-5 shrink-0" />
                Ce produit est en rupture de stock.
            </x-alert>
        @elseif ($product->is_low_stock)
            <x-alert variant="warning" class="mb-6 flex items-center gap-2">
                <x-iconoir-warning-triangle class="w-5 h-5 shrink-0" />
                Stock faible : il reste {{ $product->quantity }} {{ $product->unit->label() }}
                (seuil d'alerte : {{ $product->alert_threshold }}).
            </x-alert>
        @endif

        {{-- Stat tiles --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-card rounded-(--radius) p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-muted m-0">En stock</p>
                <p class="text-2xl font-bold m-0 mt-1 tabular-nums">
                    {{ $product->quantity }} <span class="text-base font-normal text-muted">{{ $product->unit->label() }}</span>
                </p>
            </div>
            <div class="bg-card rounded-(--radius) p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-muted m-0">Prix unitaire</p>
                <p class="text-2xl font-bold m-0 mt-1 tabular-nums">{{ number_format($product->price, 0, ',', ' ') }} <span class="text-base font-normal text-muted">FCFA</span></p>
            </div>
            <div class="bg-card rounded-(--radius) p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-muted m-0">Valeur du stock</p>
                <p class="text-2xl font-bold m-0 mt-1 tabular-nums">{{ number_format($product->stock_value, 0, ',', ' ') }} <span class="text-base font-normal text-muted">FCFA</span></p>
            </div>
            <div class="bg-card rounded-(--radius) p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-muted m-0">Seuil d'alerte</p>
                <p class="text-2xl font-bold m-0 mt-1 tabular-nums">{{ $product->alert_threshold }} <span class="text-base font-normal text-muted">{{ $product->unit->label() }}</span></p>
            </div>
        </div>

        {{-- Description --}}
        @if ($product->description)
            <x-card class="mb-6">
                <x-slot:header>
                    <h3 class="text-lg font-bold m-0">Description</h3>
                </x-slot:header>
                <p class="m-0 whitespace-pre-line text-ink">{{ $product->description }}</p>
            </x-card>
        @endif

        {{-- Movements history --}}
        <div class="bg-card rounded-(--radius) overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-5 py-4">
                <h3 class="text-lg font-bold m-0">Historique des mouvements</h3>
                <x-button :href="route('movements.create', ['product' => $product->id])" variant="ghost" size="sm">
                    <x-iconoir-plus class="w-4 h-4" />
                    Ajouter
                </x-button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wider text-muted border-t border-border">
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Type</th>
                            <th class="px-5 py-3 text-right">Quantité</th>
                            <th class="px-5 py-3 text-right">Stock</th>
                            <th class="px-5 py-3">Motif</th>
                            <th class="px-5 py-3">Par</th>
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
                                <td class="px-5 py-3 whitespace-nowrap text-muted">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-3"><x-badge :variant="$typeVariant">{{ $movement->type->label() }}</x-badge></td>
                                <td class="px-5 py-3 text-right font-semibold whitespace-nowrap tabular-nums">{{ $sign }}{{ $movement->quantity }}</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap tabular-nums text-muted">
                                    {{ $movement->stock_before }} → <span class="text-ink font-semibold">{{ $movement->stock_after }}</span>
                                </td>
                                <td class="px-5 py-3 max-w-xs truncate text-muted" title="{{ $movement->note }}">{{ $movement->note ?: '—' }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-muted">{{ $movement->user->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-muted">
                                    Aucun mouvement enregistré pour ce produit.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($movements->hasPages())
                <div class="px-5 py-3 border-t border-border">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
