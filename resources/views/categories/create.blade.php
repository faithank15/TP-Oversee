@extends('layouts.app')
@section('title', 'Nouvelle catégorie')

@section('content')
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('categories.index') }}"
            class="inline-flex items-center gap-1 text-sm font-semibold text-muted hover:text-ink no-underline mb-4">
            <x-iconoir-nav-arrow-left class="w-4 h-4" />
            Retour aux catégories
        </a>
        <h1 class="text-3xl font-bold mb-6">Nouvelle catégorie</h1>

        <x-card>
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf

                <x-input name="name" label="Nom" required autofocus />

                <x-input name="prefix" label="Préfixe" required maxlength="5" class="uppercase"
                    hint="2 à 5 lettres. Préfixe des références produits (ex. WAT → WAT-0001)." />

                <x-textarea name="description" label="Description" hint="Optionnel." />

                <div class="flex justify-end gap-3 pt-2">
                    <x-button :href="route('categories.index')" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="accent">
                        <x-iconoir-check class="w-5 h-5" />
                        Créer la catégorie
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
