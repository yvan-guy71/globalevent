@extends('layouts.app')

@section('title', 'Event Global - Créez et découvrez tous les évènements')

@section('content')
    <section id="recherche" class="relative bg-slate-900 py-24 px-4 scroll-mt-20">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('{{ asset('images/hero-home.jpg') }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900/70 to-slate-900/30"></div>
        </div>

        <div class="relative max-w-4xl mx-auto text-center">
            <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-6 reveal">
                <i class="fa-solid fa-bolt text-amber-400"></i>
                Concerts, compétitions, festivals & plus
            </span>

            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight reveal reveal-left">
                Créez et découvrez les meilleurs évènements
            </h1>
            <p class="text-xl text-indigo-100 mb-10 reveal reveal-right">
                Publiez vos concerts, compétitions ou festivals en quelques minutes, et trouvez ce qui se passe près de chez vous.
            </p>

            <form action="{{ route('events.index') }}" method="GET" id="search-form"
                  class="bg-white/10 backdrop-blur-2xl p-4 rounded-3xl shadow-2xl flex flex-col lg:flex-row gap-4 max-w-5xl mx-auto transition-all duration-500 border border-white/20 hover:border-white/40 reveal reveal-up delay-200">
                <div class="flex-1 flex items-center bg-white/10 px-6 py-3 rounded-2xl border-2 border-transparent transition-all focus-within:bg-white/20 focus-within:border-white/60 focus-within:ring-4 focus-within:ring-white/10">
                    <i class="fa-solid fa-magnifying-glass text-white mr-3"></i>
                    <input type="text" name="q" id="search-q" value="{{ request('q') }}"
                           placeholder="Rechercher un concert, une compétition, une ville..."
                           class="w-full bg-transparent border-none outline-none focus:outline-none focus:ring-0 text-white text-lg placeholder:text-white/60 font-medium">
                </div>

                <div class="lg:w-56 relative flex items-center bg-white/10 px-6 py-3 rounded-2xl border-2 border-transparent transition-all focus-within:bg-white/20 focus-within:border-white/60">
                    <select name="type" class="w-full bg-transparent border-none outline-none focus:outline-none focus:ring-0 text-white text-base font-bold appearance-none cursor-pointer">
                        <option value="" class="text-slate-800">Tous les types</option>
                        @foreach($types as $slug => $type)
                            <option value="{{ $slug }}" class="text-slate-800" {{ request('type') === $slug ? 'selected' : '' }}>
                                {{ $type['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:w-52 relative flex items-center bg-white/10 px-6 py-3 rounded-2xl border-2 border-transparent transition-all focus-within:bg-white/20 focus-within:border-white/60">
                    <select name="country" class="w-full bg-transparent border-none outline-none focus:outline-none focus:ring-0 text-white text-base font-bold appearance-none cursor-pointer">
                        <option value="" class="text-slate-800">Tous les pays</option>
                        @foreach($countries as $country)
                            <option value="{{ $country }}" class="text-slate-800" {{ request('country') === $country ? 'selected' : '' }}>
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-2xl font-black text-lg flex items-center justify-center gap-2 transition-colors">
                    Rechercher
                </button>
            </form>

            <div class="mt-6 flex flex-wrap items-center justify-center gap-3 reveal reveal-up delay-400">
                <span class="text-indigo-100/80 text-sm font-bold uppercase tracking-wider mr-2">Populaire</span>
                @foreach(['Concert', 'Compétition', 'Festival', 'Sport', 'Lomé'] as $suggestion)
                    <a href="{{ route('events.index', ['q' => $suggestion]) }}"
                       class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-4 py-1.5 rounded-full text-sm font-bold hover:bg-white hover:text-indigo-600 transition-all hover:-translate-y-1 shadow-sm">
                        {{ $suggestion }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 -mt-10 relative z-10">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-strong border border-slate-100 dark:border-slate-800 grid grid-cols-2 lg:grid-cols-4 divide-x divide-slate-100 dark:divide-slate-800 reveal reveal-up">
            @php
                $statBlocks = [
                    ['icon' => 'fa-calendar-star', 'value' => $stats['events'], 'label' => 'Évènements publiés'],
                    ['icon' => 'fa-location-dot', 'value' => $stats['cities'], 'label' => 'Villes couvertes'],
                    ['icon' => 'fa-user-tie', 'value' => $stats['organizers'], 'label' => 'Organisateurs'],
                    ['icon' => 'fa-ticket', 'value' => $stats['free'], 'label' => 'Évènements gratuits'],
                ];
            @endphp
            @foreach($statBlocks as $block)
                <div class="p-6 text-center">
                    <i class="fa-solid {{ $block['icon'] }} text-indigo-600 dark:text-indigo-400 text-xl mb-2"></i>
                    <div class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($block['value'], 0, ',', ' ') }}</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-1">{{ $block['label'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section id="categories" class="max-w-7xl mx-auto py-20 px-4 scroll-mt-20">
        <div class="flex items-center justify-between mb-4 reveal reveal-left">
            <div>
                <h2 class="text-3xl font-bold">Explorez par catégorie</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2">Concerts, compétitions, festivals, conférences... trouvez l'évènement qui vous ressemble.</p>
            </div>
            <a href="{{ route('events.index') }}" class="hidden sm:inline-block text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">Voir tout</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mt-8">
            @foreach($types as $index => $type)
                @php
                    $delay = ($loop->index % 5) * 100;
                    $active = request('type') === $index;
                @endphp
                <div class="relative group rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition h-40 md:h-48 reveal reveal-up {{ $active ? 'ring-4 ring-indigo-500' : '' }}"
                     style="transition-delay: {{ $delay }}ms">
                    <a href="{{ route('events.index', ['type' => $index]) }}" class="absolute inset-0 z-20" aria-label="{{ $type['label'] }}"></a>
                    <img src="{{ asset($type['image']) }}" alt="{{ $type['label'] }}" loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4 bg-black/45 text-white transition-colors duration-300 z-10 group-hover:bg-black/60">
                        <i class="fa-solid {{ $type['icon'] }} text-xl mb-2"></i>
                        <h3 class="font-bold text-lg md:text-xl">{{ $type['label'] }}</h3>
                        <p class="text-sm opacity-90 mt-1">
                            {{ $typeCounts[$index] ?? 0 }} évènement{{ ($typeCounts[$index] ?? 0) > 1 ? 's' : '' }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section id="featured" class="bg-slate-100 dark:bg-slate-900/60 py-20 px-4 scroll-mt-20">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-12 reveal reveal-right">
                <div>
                    <h2 class="text-3xl font-bold">Évènements à la une</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2">La sélection de la rédaction Event Global.</p>
                </div>
                <a href="{{ route('events.index') }}" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">Tout voir</a>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($featured as $index => $event)
                    <div class="reveal reveal-up" style="transition-delay: {{ ($loop->index % 3) * 150 }}ms">
                        @include('events.partials.card', ['event' => $event])
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-slate-500 dark:text-slate-400">Aucun évènement à la une pour le moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto py-20 px-4">
        <div class="flex items-center justify-between mb-12 reveal reveal-left">
            <div>
                <h2 class="text-3xl font-bold">Prochains évènements</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2">Ne manquez pas ce qui arrive bientôt.</p>
            </div>
            <a href="{{ route('events.index', ['when' => 'upcoming']) }}" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">Voir tout</a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($upcoming as $index => $event)
                <div class="reveal reveal-up" style="transition-delay: {{ ($loop->index % 3) * 120 }}ms">
                    @include('events.partials.card', ['event' => $event])
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-12 text-center">
                    <i class="fa-regular fa-calendar text-4xl text-slate-300 mb-4"></i>
                    <p class="text-slate-500 dark:text-slate-400">Aucun évènement à venir. Soyez le premier à publier !</p>
                    <a href="{{ route('events.create') }}" class="inline-block mt-6 px-6 py-3 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700">Créer un évènement</a>
                </div>
            @endforelse
        </div>
    </section>

    <section class="bg-slate-900 py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold text-white text-center mb-4 reveal reveal-up">Comment ça marche ?</h2>
            <p class="text-indigo-100/80 text-center mb-14 reveal reveal-up delay-100">Trois étapes suffisent pour lancer votre évènement.</p>

            @php
                $steps = [
                    ['icon' => 'fa-user-plus', 'title' => '1. Créez votre compte', 'text' => 'Inscrivez-vous gratuitement en moins d\'une minute pour devenir organisateur.'],
                    ['icon' => 'fa-pen-to-square', 'title' => '2. Publiez l\'évènement', 'text' => 'Titre, date, lieu, prix, image : renseignez les informations et validez.'],
                    ['icon' => 'fa-rocket', 'title' => '3. Faites-en parler', 'text' => 'Votre évènement est visible par tous et partageable sur vos réseaux.'],
                ];
            @endphp

            <div class="grid md:grid-cols-3 gap-8">
                @foreach($steps as $i => $step)
                    <div class="bg-white/5 backdrop-blur border border-white/10 rounded-3xl p-8 reveal reveal-up" style="transition-delay: {{ $i * 150 }}ms">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-xl mb-6">
                            <i class="fa-solid {{ $step['icon'] }}"></i>
                        </div>
                        <h3 class="text-white font-black text-lg mb-3">{{ $step['title'] }}</h3>
                        <p class="text-indigo-100/70 text-sm leading-relaxed">{{ $step['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-20 px-4 text-center">
        <div class="max-w-3xl mx-auto bg-indigo-600 rounded-3xl p-12 reveal reveal-up">
            <h2 class="text-3xl font-bold text-white mb-6">Prêt à faire vibrer votre public ?</h2>
            <p class="text-indigo-100 mb-10 text-lg">
                Créez votre évènement en quelques minutes et donnez-lui la visibilité qu'il mérite.
            </p>
            @auth
                <a href="{{ route('events.create') }}" class="inline-block bg-white text-indigo-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-slate-100 transition shadow-lg">
                    Créer un évènement
                </a>
            @else
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="inline-block bg-white text-indigo-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-slate-100 transition shadow-lg">
                        Créer mon compte gratuitement
                    </a>
                    <a href="{{ route('events.index') }}" class="inline-block bg-indigo-500/40 border border-white/30 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-indigo-500/60 transition">
                        Découvrir les évènements
                    </a>
                </div>
            @endauth
        </div>
    </section>
@endsection
