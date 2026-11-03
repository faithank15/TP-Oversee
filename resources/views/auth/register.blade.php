@extends('layouts.app')
@section('title', 'Créer un compte')
@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center gap-6 p-4">
        <a href="{{ route('login') }}" class="flex items-center gap-3 no-underline">
            <x-app-logo class="h-16 w-auto" />
            <span class="text-3xl font-extrabold tracking-tight text-ink">Oversee</span>
        </a>
        <x-card class="w-full max-w-md">
            <x-slot:header>
                <h1 class="text-3xl font-bold m-0">Créer un compte</h1>
                <p class="text-muted m-0 mt-1">Inscrivez-vous pour gérer votre inventaire.</p>
            </x-slot:header>

            <form method="POST" action="{{ route('register') }}" class="flex flex-col">
                @csrf

                <x-input name="shop_name" label="Nom de la boutique" required autofocus />
                <x-input name="name" label="Nom" required />
                <x-input name="email" label="Adresse e-mail" type="email" required />
                <x-input name="password" label="Mot de passe" type="password" required />
                <x-input name="password_confirmation" label="Confirmer le mot de passe" type="password" required />

                <x-button type="submit" variant="accent" block>Créer mon compte</x-button>
            </form>

            <p class="text-center text-sm text-muted mt-5 mb-0">
                Déjà inscrit ?
                <a href="{{ route('login') }}"
                    class="font-semibold text-primary underline decoration-accent decoration-2 underline-offset-2 hover:decoration-[3px]">Se
                    connecter</a>
            </p>
        </x-card>
    </div>
@endsection
