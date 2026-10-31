@extends('layouts.app')
@section('title', 'Nouveau produit')

@section('content')
    @php
        $categoryOptions = $categories->pluck('name', 'id')->all();
        $unitOptions = collect($units)->mapWithKeys(fn ($unit) => [$unit->value => $unit->label()])->all();
    @endphp

    <div class="max-w-3xl">
        {{-- Header --}}
        <a href="{{ route('products.index') }}"
            class="inline-flex items-center gap-1 text-sm font-semibold text-muted hover:text-ink no-underline mb-4">
            <x-iconoir-nav-arrow-left class="w-4 h-4" />
            Retour aux produits
        </a>
        <h1 class="text-3xl font-bold mb-6">Nouveau produit</h1>

        <x-card>
            <form method="POST" action="{{ route('products.store') }}">
                @csrf

                <x-input name="name" label="Nom du produit" required autofocus />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <x-select name="category_id" label="Catégorie" :options="$categoryOptions"
                        placeholder="Choisir une catégorie" required />

                    <x-input name="reference" label="Référence"
                        hint="Laissez vide pour générer une référence automatiquement." />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <x-select name="unit" label="Unité" :options="$unitOptions"
                        placeholder="Choisir une unité" required />

                    <x-input name="price" label="Prix unitaire (FCFA)" type="number" min="0" required />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <x-input name="initial_quantity" label="Quantité initiale" type="number" min="0" value="0"
                        required hint="Stock disponible au moment de la création." />

                    <x-input name="alert_threshold" label="Seuil d'alerte" type="number" min="0" value="5"
                        required hint="Alerte lorsque le stock atteint ce niveau." />
                </div>

                <x-textarea name="description" label="Description"
                    hint="Optionnel — détails, variantes, notes internes…" />

                <div class="flex justify-end gap-3 pt-2">
                    <x-button :href="route('products.index')" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="accent">
                        <x-iconoir-check class="w-5 h-5" />
                        Enregistrer le produit
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
