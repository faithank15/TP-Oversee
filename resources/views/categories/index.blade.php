@extends('layouts.app')
@section('title', 'Catégories')

@section('content')
    <div class="">
        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold m-0">Catégories</h1>
                <p class="text-muted m-0 mt-1">Organisez vos produits et leurs préfixes de référence.</p>
            </div>
            <x-button :href="route('categories.create')" variant="accent">
                <x-iconoir-plus class="w-5 h-5" />
                Nouvelle catégorie
            </x-button>
        </div>

        @if (session('error'))
            <x-alert variant="danger" class="mb-4">{{ session('error') }}</x-alert>
        @endif

        {{-- Vertical stack of cards --}}
        <div class="flex flex-col gap-2 max-w-3xl">
            @forelse ($categories as $category)
                <div class="bg-card rounded-(--radius) p-2 flex items-center gap-4">
                    <span class="w-12 h-12 rounded-(--radius-sm) bg-primary text-white font-bold text-sm grid place-content-center shrink-0 uppercase tracking-wide">
                        {{ $category->prefix }}
                    </span>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-lg font-bold m-0 truncate">{{ $category->name }}</h3>
                            <x-badge>
                                {{ $category->products_count }} {{ \Illuminate\Support\Str::plural('produit', $category->products_count) }}
                            </x-badge>
                        </div>
                        <p class="text-muted text-sm m-0 mt-1 truncate">
                            {{ $category->description ?: 'Aucune description' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <x-button :href="route('categories.edit', $category)" variant="ghost" size="sm">
                            <x-iconoir-edit class="w-4 h-4" />
                            Modifier
                        </x-button>

                        <form method="POST" action="{{ route('categories.destroy', $category) }}"
                            onsubmit="return confirm('Supprimer la catégorie « {{ $category->name }} » ?');">
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" variant="danger" size="sm">
                                <x-iconoir-trash class="w-4 h-4" />
                                <span class="sr-only">Supprimer</span>
                            </x-button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-card rounded-(--radius) p-12 text-center">
                    <x-iconoir-view-grid class="w-12 h-12 mx-auto text-muted mb-3" />
                    <p class="font-semibold text-ink m-0">Aucune catégorie</p>
                    <p class="text-muted mt-1 mb-4">Créez une première catégorie pour classer vos produits.</p>
                    <x-button :href="route('categories.create')" variant="accent">
                        <x-iconoir-plus class="w-5 h-5" />
                        Nouvelle catégorie
                    </x-button>
                </div>
            @endforelse
        </div>
    </div>
@endsection
