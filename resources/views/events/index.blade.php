@extends('layouts.app')

@section('title', 'Tous les évènements - Event Global')

@section('content')
    <section class="bg-slate-900 pt-16 pb-24 px-4 relative">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?auto=format&fit=crop&q=80')] bg-cover bg-center opacity-30"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900/80 to-slate-900/40"></div>
        </div>
        <div class="relative max-w-7xl mx-auto">
            <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-4 reveal reveal-left">Tous les évènements</h1>
            <p class="text-indigo-100 reveal reveal-right">
                {{ $events->total() }} évènement{{ $events->total() > 1 ? 's' : '' }} trouvé{{ $events->total() > 1 ? 's' : '' }}
                @if($filters['q'] || $filters['type'] || $filters['country'] || $filters['city']) correspondant à votre recherche @endif
            </p>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 -mt-12 relative z-10 pb-20">
        <form method="GET" action="{{ route('events.index') }}"
              class="bg-white dark:bg-slate-900 rounded-3xl shadow-strong border border-slate-100 dark:border-slate-800 p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Rechercher un évènement, un lieu..."
                       class="w-full pl-11 pr-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
            </div>

            <select name="type" class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">Tous les types</option>
                @foreach($types as $slug => $type)
                    <option value="{{ $slug }}" {{ $filters['type'] === $slug ? 'selected' : '' }}>{{ $type['label'] }}</option>
                @endforeach
            </select>

            <select name="country" class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">Tous les pays</option>
                @foreach($countries as $country)
                    <option value="{{ $country }}" {{ $filters['country'] === $country ? 'selected' : '' }}>{{ $country }}</option>
                @endforeach
            </select>

            <select name="city" class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">Toutes les villes</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}" {{ $filters['city'] === $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>

            <div class="flex gap-3">
                <select name="when" class="flex-1 px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="upcoming" {{ $filters['when'] !== 'past' && $filters['when'] !== 'free' ? 'selected' : '' }}>À venir</option>
                    <option value="past" {{ $filters['when'] === 'past' ? 'selected' : '' }}>Passés</option>
                    <option value="free" {{ $filters['when'] === 'free' ? 'selected' : '' }}>Gratuits</option>
                </select>
                <select name="sort" class="flex-1 px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="" {{ $filters['sort'] === '' ? 'selected' : '' }}>Bientôt</option>
                    <option value="recent" {{ $filters['sort'] === 'recent' ? 'selected' : '' }}>Récents</option>
                    <option value="price_asc" {{ $filters['sort'] === 'price_asc' ? 'selected' : '' }}>Prix ↑</option>
                    <option value="price_desc" {{ $filters['sort'] === 'price_desc' ? 'selected' : '' }}>Prix ↓</option>
                </select>
            </div>

            <div class="md:col-span-2 lg:col-span-6 flex flex-wrap items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
                    <i class="fa-solid fa-filter mr-2"></i>Filtrer
                </button>
                @if($filters['q'] || $filters['type'] || $filters['country'] || $filters['city'] || $filters['when'] || $filters['sort'])
                    <a href="{{ route('events.index') }}" class="px-6 py-3 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800">
                        Réinitialiser
                    </a>
                @endif
                <a href="{{ route('events.create') }}" class="ml-auto px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-2xl hover:opacity-90 transition-opacity">
                    <i class="fa-solid fa-plus mr-2"></i>Créer un évènement
                </a>
            </div>
        </form>

        <div class="mt-10">
            @if($events->count())
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($events as $event)
                        @include('events.partials.card', ['event' => $event])
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $events->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-16 text-center">
                    <i class="fa-regular fa-face-frown text-5xl text-slate-300 dark:text-slate-600 mb-6"></i>
                    <h2 class="text-xl font-black mb-2">Aucun évènement ne correspond à votre recherche</h2>
                    <p class="text-slate-500 dark:text-slate-400 mb-8">Essayez d'élargir vos filtres ou créez le vôtre !</p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('events.index') }}" class="px-6 py-3 border border-slate-200 dark:border-slate-700 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-800">Voir tous les évènements</a>
                        <a href="{{ route('events.create') }}" class="px-6 py-3 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700">Créer un évènement</a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
