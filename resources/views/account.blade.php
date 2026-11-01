@extends('layouts.app')
@section('title', 'Mon compte')

@section('content')
    @php
        $user = auth()->user();
        $initial = \Illuminate\Support\Str::substr($user->shop_name ?: $user->name, 0, 1);
    @endphp

    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold m-0">Mon compte</h1>
        <p class="text-muted m-0 mt-1 mb-6">Vos informations personnelles.</p>

        {{-- Identity summary --}}
        <x-card class="mb-6">
            <div class="flex items-center gap-4">
                <span class="w-16 h-16 rounded-(--radius) bg-primary text-white font-extrabold text-2xl grid place-content-center shrink-0 uppercase">
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

        {{-- Profile details (read-only) --}}
        <x-card>
            <x-slot:header>
                <h3 class="text-lg font-bold m-0">Informations du profil</h3>
            </x-slot:header>

            <dl class="divide-y divide-border">
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm font-semibold text-muted">Nom de la boutique</dt>
                    <dd class="text-ink m-0 text-right truncate">{{ $user->shop_name }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm font-semibold text-muted">Nom</dt>
                    <dd class="text-ink m-0 text-right truncate">{{ $user->name }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm font-semibold text-muted">Adresse e-mail</dt>
                    <dd class="text-ink m-0 text-right truncate">{{ $user->email }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm font-semibold text-muted">Membre depuis</dt>
                    <dd class="text-ink m-0 text-right">{{ $user->created_at?->translatedFormat('d F Y') }}</dd>
                </div>
            </dl>

            <p class="text-sm text-muted mt-4 mb-0 flex items-center gap-2">
                <x-iconoir-info-circle class="w-4 h-4 shrink-0" />
                La modification du profil n'est pas disponible pour le moment.
            </p>
        </x-card>
    </div>
@endsection
