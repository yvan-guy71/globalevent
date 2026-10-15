@extends('layouts.app')

@section('title', 'Cagnottes solidaires - Event Global')

@section('content')
    <section class="bg-slate-900 py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div>
                    <span class="inline-flex items-center gap-2 bg-rose-500 text-white text-xs font-black uppercase tracking-wider px-4 py-2 rounded-full mb-5">
                        <i class="fa-solid fa-hand-holding-heart"></i> Cagnottes
                    </span>
                    <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-4">Soutenez les projets qui vous tiennent à cœur</h1>
                    <p class="text-lg text-indigo-100 max-w-2xl">
                        Concerts, compétitions, causes solidaires : ouvrez une cagnotte et collectez des dons en ligne.
                    </p>
                </div>
                <a href="{{ route('fundraisers.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-white text-indigo-600 font-black rounded-2xl hover:bg-slate-100 transition-colors shrink-0">
                    <i class="fa-solid fa-plus"></i> Ouvrir une cagnotte
                </a>
            </div>

            <form method="GET" action="{{ route('fundraisers.index') }}" class="mt-10 max-w-xl relative">
                <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="q" value="{{ $search }}" placeholder="Rechercher une cagnotte..."
                       class="w-full pl-14 pr-4 py-4 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-indigo-200/60 font-medium focus:ring-2 focus:ring-indigo-400 outline-none">
            </form>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 py-16">
        @if($fundraisers->count())
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($fundraisers as $fundraiser)
                    @include('fundraisers.partials.card', ['fundraiser' => $fundraiser])
                @endforeach
            </div>

            <div class="mt-12">
                {{ $fundraisers->links() }}
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-16 text-center">
                <i class="fa-solid fa-hand-holding-heart text-5xl text-slate-300 dark:text-slate-600 mb-6"></i>
                <h2 class="text-xl font-black mb-2">Aucune cagnotte pour l'instant</h2>
                <p class="text-slate-500 dark:text-slate-400 mb-8">Soyez le premier à lancer une collecte solidaire.</p>
                <a href="{{ route('fundraisers.create') }}" class="inline-block px-8 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700">
                    Ouvrir une cagnotte
                </a>
            </div>
        @endif
    </section>
@endsection
