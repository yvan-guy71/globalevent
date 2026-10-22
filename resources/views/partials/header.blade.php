<header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur border-b border-slate-100 dark:border-slate-800 sticky top-0 z-50">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <div class="flex items-center gap-4 lg:gap-10">
            <button type="button" onclick="toggleMobileMenu()" class="lg:hidden p-2 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            @include('partials.logo')

            <div class="hidden lg:flex items-center gap-6 ml-4">
                <a href="{{ route('events.index') }}" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Évènements</a>
                <a href="{{ route('fundraisers.index') }}" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Cagnottes</a>
                <a href="{{ route('home') }}#categories" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Catégories</a>
                <a href="{{ route('home') }}#featured" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">À la une</a>
                <a href="{{ route('pages.how') }}" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Comment ça marche</a>
                <div class="h-4 w-[1px] bg-slate-200 dark:bg-slate-700 mx-2"></div>
                <a href="{{ route('events.create') }}" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">Créer un évènement</a>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative">
                <button type="button"
                        id="settings-button"
                        onclick="toggleSettingsDropdown()"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                        aria-label="Préférences">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                    </svg>
                </button>
                <div id="settings-dropdown"
                     class="preferences-menu fixed top-20 inset-x-4 sm:absolute sm:top-auto sm:inset-x-auto sm:right-0 mt-2 w-auto sm:w-52 max-w-xs rounded-xl bg-white dark:bg-slate-800 shadow-lg dark:shadow-slate-900 border border-slate-100 dark:border-slate-700 text-sm z-50 hidden">
                    <a href="{{ route('pages.how') }}" class="w-full block px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Comment ça marche</a>
                    <a href="{{ route('pages.contact') }}" class="w-full block px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Contact</a>
                    <button type="button"
                            onclick="toggleTheme()"
                            class="w-full flex items-center justify-between px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                        <span>Thème sombre</span>
                        <span class="flex items-center gap-1 text-slate-500 dark:text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 3.25a1 1 0 0 1 1 1V6a1 1 0 1 1-2 0V4.25a1 1 0 0 1 1-1zm0 10a3 3 0 1 0-3-3 3 3 0 0 0 3 3zm8.75-3a1 1 0 0 1-1 1H18a1 1 0 0 1 0-2h1.75a1 1 0 0 1 1 1zM12 17a1 1 0 0 1 1 1v1.75a1 1 0 1 1-2 0V18a1 1 0 0 1 1-1zM7 12a1 1 0 0 1-1 1H4.25a1 1 0 0 1 0-2H6a1 1 0 0 1 1 1zm9.19 4.19a1 1 0 0 1 1.41 0l1.24 1.24a1 1 0 1 1-1.41 1.41l-1.24-1.24a1 1 0 0 1 0-1.41zM5.16 5.16a1 1 0 0 1 1.41 0L7.81 6.4A1 1 0 0 1 6.4 7.81L5.16 6.57a1 1 0 0 1 0-1.41z"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21.64 13.65A9 9 0 1 1 10.35 2.36 7 7 0 0 0 21.64 13.65z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>

            @auth
                <a href="{{ route('events.create') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 text-sm font-black text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors shadow-md shadow-indigo-100 dark:shadow-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Créer
                </a>

                <div class="relative">
                    <button type="button"
                            id="user-menu-button"
                            onclick="toggleUserMenu()"
                            class="inline-flex items-center gap-2 rounded-xl p-1 pr-2 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                            aria-label="Mon compte">
                        <span class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold shadow-sm">
                            {{ auth()->user()->initials() }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="user-menu-dropdown"
                         class="preferences-menu fixed top-20 inset-x-4 sm:absolute sm:top-auto sm:inset-x-auto sm:right-0 mt-2 w-auto sm:w-60 max-w-xs rounded-xl bg-white dark:bg-slate-800 shadow-lg dark:shadow-slate-900 border border-slate-100 dark:border-slate-700 text-sm z-50 hidden">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                            <p class="font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('events.mine') }}" class="flex items-center gap-3 px-4 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Mes évènements
                            </a>
                            <a href="{{ route('fundraisers.mine') }}" class="flex items-center gap-3 px-4 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                Mes cagnottes
                            </a>
                            <a href="{{ route('tickets.index') }}" class="flex items-center gap-3 px-4 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                                Mes billets
                            </a>
                            <a href="{{ route('events.mine') }}" class="flex items-center gap-3 px-4 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                                </svg>
                                Scanner un billet
                            </a>
                            <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                Mes commandes
                            </a>
                        </div>
                        @if(auth()->user()->isClient())
                            <div class="py-1 border-t border-slate-100 dark:border-slate-700">
                                <form action="{{ route('become-organizer') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/40">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.98 10.1c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        Devenir organisateur
                                    </button>
                                </form>
                            </div>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <div class="py-1 border-t border-slate-100 dark:border-slate-700">
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    Panel admin
                                </a>
                            </div>
                        @endif
                        <div class="py-1 border-t border-slate-100 dark:border-slate-700">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Se déconnecter
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 transition-all">Connexion</a>
                <a href="{{ route('register') }}" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-black rounded-xl hover:bg-indigo-700 transition-colors shadow-md shadow-indigo-100 dark:shadow-none">
                    Inscription
                </a>
            @endauth
        </div>
    </nav>

    <div id="mobile-menu" class="fixed inset-0 z-40 bg-black/40 hidden lg:hidden">
        <div id="mobile-menu-content" class="absolute left-0 top-0 h-full w-72 max-w-full bg-white dark:bg-slate-900 shadow-xl transform -translate-x-full transition-transform duration-300">
            <div class="flex items-center justify-between px-4 py-4 border-b border-slate-100 dark:border-slate-800">
                @include('partials.logo', ['size' => 'sm'])
                <button type="button" onclick="toggleMobileMenu()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-4 py-4 space-y-2">
                <a href="{{ route('events.index') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Évènements</a>
                <a href="{{ route('fundraisers.index') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Cagnottes</a>
                <a href="{{ route('home') }}#categories" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Catégories</a>
                <a href="{{ route('home') }}#featured" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">À la une</a>
                <a href="{{ route('pages.how') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Comment ça marche</a>
                <a href="{{ route('pages.contact') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Contact</a>
            </div>
            <div class="px-4 py-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                @auth
                    <a href="{{ route('events.create') }}" class="block w-full text-center px-4 py-2.5 text-sm font-black text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl">Créer un évènement</a>
                    <a href="{{ route('events.mine') }}" class="block w-full text-center px-4 py-2.5 text-sm font-bold text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900 rounded-xl">Mes évènements</a>
                    <a href="{{ route('fundraisers.mine') }}" class="block w-full text-center px-4 py-2.5 text-sm font-bold text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900 rounded-xl">Mes cagnottes</a>
                    <a href="{{ route('tickets.index') }}" class="block w-full text-center px-4 py-2.5 text-sm font-bold text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900 rounded-xl">Mes billets</a>
                    <a href="{{ route('orders.index') }}" class="block w-full text-center px-4 py-2.5 text-sm font-bold text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900 rounded-xl">Mes commandes</a>
                    @if(auth()->user()->isClient())
                        <form action="{{ route('become-organizer') }}" method="POST">
                            @csrf
                            <button type="submit" class="block w-full text-center px-4 py-2.5 text-sm font-black text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl">Devenir organisateur</button>
                        </form>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block w-full text-center px-4 py-2.5 text-xs font-black text-white bg-slate-900 rounded-xl">PANEL ADMIN</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 text-xs font-bold text-slate-500 hover:text-rose-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800">Se déconnecter</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl">Se connecter</a>
                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2.5 text-sm font-bold text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900 rounded-xl">Créer un compte</a>
                @endauth
            </div>
        </div>
    </div>
</header>
