@extends('layouts.app')
@section('title', 'Connexion')
@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center gap-6 p-4">
        <a href="{{ route('login') }}" class="flex items-center gap-3 no-underline">
            <x-app-logo class="h-16 w-auto" />
            <span class="text-3xl font-extrabold tracking-tight text-ink">Oversee</span>
        </a>
        <x-card class="w-full max-w-md">
            <x-slot:header>
                <h1 class="text-3xl font-bold m-0">Connexion</h1>
                <p class="text-muted m-0 mt-1">Accédez à votre espace Oversee.</p>
            </x-slot:header>

            <form method="POST" action="{{ route('login') }}" class="flex flex-col">
                @csrf

                <x-input name="email" label="Adresse e-mail" type="email" required autofocus />
                <x-input name="password" label="Mot de passe" type="password" required />

                <label class="flex items-center gap-2 mb-4 font-medium cursor-pointer">
                    <input type="checkbox" name="remember" class="w-5 h-5 rounded-sm accent-accent">
                    Rester connecté
                </label>

                <x-button type="submit" variant="accent" block>Se connecter</x-button>
            </form>

            <p class="text-center text-sm text-muted mt-5 mb-0">
                Pas encore de compte ?
                <a href="{{ route('register') }}"
                    class="font-semibold text-primary underline decoration-accent decoration-2 underline-offset-2 hover:decoration-[3px]">S'inscrire</a>
            </p>
        </x-card>
    </div>
@endsection
