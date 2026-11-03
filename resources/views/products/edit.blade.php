@extends('layouts.app')
@section('title', 'Modifier le produit')

@section('content')
    @php
        $categoryOptions = $categories->pluck('name', 'id')->all();
        $unitOptions = collect($units)->mapWithKeys(fn ($unit) => [$unit->value => $unit->label()])->all();
    @endphp

    <div class="max-w-3xl mx-auto">
        <a href="{{ route('products.show', $product) }}"
            class="inline-flex items-center gap-1 text-sm font-semibold text-muted hover:text-ink no-underline mb-4">
            <x-iconoir-nav-arrow-left class="w-4 h-4" />
            Retour au produit
        </a>
        <h1 class="text-3xl font-bold mb-6">Modifier le produit</h1>

        <x-card>
            <form method="POST" action="{{ route('products.update', $product) }}">
                @csrf
                @method('PUT')

                <x-input name="name" label="Nom du produit" :value="$product->name" required autofocus />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <x-select name="category_id" label="Catégorie" :options="$categoryOptions"
                        :value="$product->category_id" placeholder="Choisir une catégorie" required />

                    <x-input name="reference" label="Référence" :value="$product->reference"
                        hint="Laissez vide pour conserver la référence actuelle." />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <x-select name="unit" label="Unité" :options="$unitOptions"
                        :value="$product->unit->value" placeholder="Choisir une unité" required />

                    <x-input name="price" label="Prix unitaire (FCFA)" type="number" min="0"
                        :value="$product->price" required />
                </div>

                <x-input name="alert_threshold" label="Seuil d'alerte" type="number" min="0"
                    :value="$product->alert_threshold" required
                    hint="Alerte lorsque le stock atteint ce niveau." />

                <x-textarea name="description" label="Description" :value="$product->description"
                    hint="Optionnel — détails, variantes, notes internes…" />

                <div class="flex items-center justify-between gap-3 pt-2">
                    <p class="text-sm text-muted m-0">
                        La quantité en stock se modifie via les
                        <a href="{{ route('movements.create', ['product' => $product->id]) }}"
                            class="font-semibold text-primary underline decoration-accent decoration-2 underline-offset-2">mouvements</a>.
                    </p>
                    <div class="flex gap-3 shrink-0">
                        <x-button :href="route('products.show', $product)" variant="ghost">Annuler</x-button>
                        <x-button type="submit" variant="accent">
                            <x-iconoir-check class="w-5 h-5" />
                            Enregistrer
                        </x-button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
@endsection
