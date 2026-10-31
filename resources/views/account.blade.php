@extends('layouts.app')
@section('title', 'Mon compte')

@section('content')
    @php
        $user = auth()->user();
        $initial = \Illuminate\Support\Str::substr($user->shop_name ?: $user->name, 0, 1);
    @endphp

    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold m-0">Mon compte</h1>
        <p class="text-muted m-0 mt-1 mb-6">Gérez vos informations personnelles et votre sécurité.</p>

        {{-- Identity summary --}}
        <x-card class="mb-6">
            <div class="flex items-center gap-4">
                <span class="w-16 h-16 rounded-(--radius) bg-linear-to-br from-accent to-primary text-white font-extrabold text-2xl grid place-content-center shrink-0 uppercase">
                    {{ $initial }}
                </span>
                <div class="min-w-0">
                    <h2 class="text-xl font-bold m-0 truncate">{{ $user->shop_name }}</h2>
                    <p class="text-muted m-0 truncate">{{ $user->name }} · {{ $user->email }}</p>
                    <p class="text-sm text-muted m-0 mt-1">
                        Membre depuis {{ $user->created_at?->translatedFormat('F Y') }}
                    </p>
                </div>
            </div>
        </x-card>

        {{-- Profile information --}}
        <x-card class="mb-6">
            <x-slot:header>
                <h3 class="text-lg font-bold m-0">Informations du profil</h3>
                <small class="text-muted">Nom de la boutique, nom et adresse e-mail.</small>
            </x-slot:header>

            <form method="POST" action="#">
                @csrf

                <x-input name="shop_name" label="Nom de la boutique" :value="$user->shop_name" required />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <x-input name="name" label="Nom" :value="$user->name" required />
                    <x-input name="email" label="Adresse e-mail" type="email" :value="$user->email" required />
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <span class="text-xs text-muted mr-auto">Enregistrement bientôt disponible.</span>
                    <x-button type="submit" variant="accent" disabled>
                        <x-iconoir-check class="w-5 h-5" />
                        Enregistrer
                    </x-button>
                </div>
            </form>
        </x-card>

        {{-- Password --}}
        <x-card>
            <x-slot:header>
                <h3 class="text-lg font-bold m-0">Mot de passe</h3>
                <small class="text-muted">Choisissez un mot de passe long et unique.</small>
            </x-slot:header>

            <form method="POST" action="#">
                @csrf

                <x-input name="current_password" label="Mot de passe actuel" type="password" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <x-input name="password" label="Nouveau mot de passe" type="password" />
                    <x-input name="password_confirmation" label="Confirmer le mot de passe" type="password" />
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <span class="text-xs text-muted mr-auto">Enregistrement bientôt disponible.</span>
                    <x-button type="submit" variant="accent" disabled>
                        <x-iconoir-check class="w-5 h-5" />
                        Mettre à jour
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
