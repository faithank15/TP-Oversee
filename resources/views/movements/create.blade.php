@extends('layouts.app')
@section('title', 'Nouveau mouvement')

@section('content')
    @php
        $productOptions = $products->pluck('name', 'id')->all();
        $typeOptions = collect($types)->mapWithKeys(fn ($type) => [$type->value => $type->label()])->all();
    @endphp

    <div class="max-w-2xl">
        <a href="{{ route('movements.index') }}"
            class="inline-flex items-center gap-1 text-sm font-semibold text-muted hover:text-ink no-underline mb-4">
            <x-iconoir-nav-arrow-left class="w-4 h-4" />
            Retour aux mouvements
        </a>
        <h1 class="text-3xl font-bold mb-6">Nouveau mouvement</h1>

        <x-card>
            <form method="POST" action="{{ route('movements.store') }}">
                @csrf

                <x-select name="product_id" label="Produit" :options="$productOptions"
                    :value="$selectedProductId" placeholder="Choisir un produit" required />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <x-select name="type" label="Type de mouvement" :options="$typeOptions"
                        placeholder="Choisir un type" required />

                    <x-input name="quantity" label="Quantité" type="number" min="0" required
                        hint="Au moins 1 pour une entrée ou une sortie." />
                </div>

                <x-textarea name="note" label="Motif"
                    hint="Optionnel, sauf pour un ajustement où il est obligatoire." />

                <div class="flex justify-end gap-3 pt-2">
                    <x-button :href="route('movements.index')" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="accent">
                        <x-iconoir-check class="w-5 h-5" />
                        Enregistrer le mouvement
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
