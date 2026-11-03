<aside
    {{ $attributes->class('w-64 shrink-0 bg-card rounded-(--radius) py-5 px-4 flex flex-col sticky top-4 self-start h-[calc(100vh-2rem)] overflow-y-auto') }}>

    <a href="{{ route('dashboard') }}" class="flex items-center px-2 gap-3 mb-8 no-underline">
        <x-app-logo class="w-auto shrink-0" />
        <span class="text-xl font-extrabold tracking-tight text-ink">Oversee</span>
    </a>

    <p class="px-4 mb-2 text-xs font-bold uppercase tracking-wider text-muted">Menu</p>
    <nav class="flex flex-col gap-1">
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <x-iconoir-reports   class="w-6 h-6 shrink-0" />
            Tableau de bord
        </x-nav-link>

        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
            <x-iconoir-box-iso class="w-6 h-6 shrink-0" />
            Produits
        </x-nav-link>

        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
            <x-iconoir-view-grid class="w-6 h-6 shrink-0" />
            Catégories
        </x-nav-link>

        <x-nav-link :href="route('movements.index')" :active="request()->routeIs('movements.*')">
            <x-iconoir-data-transfer-both class="w-6 h-6 shrink-0" />
            Mouvements
        </x-nav-link>
    </nav>

    <p class="px-4 mt-auto pt-8 mb-2 text-xs font-bold uppercase tracking-wider text-muted">Compte</p>
    <nav class="flex flex-col gap-1 mb-1">
        <x-nav-link :href="route('account')" :active="request()->routeIs('account')">
            <x-iconoir-user class="w-6 h-6 shrink-0" />
            Mon compte
        </x-nav-link>
    </nav>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-(--radius) font-semibold text-ink hover:bg-danger-soft hover:text-danger transition cursor-pointer">
            <x-iconoir-log-out class="w-6 h-6 shrink-0" />
            Se déconnecter
        </button>
    </form>
</aside>
