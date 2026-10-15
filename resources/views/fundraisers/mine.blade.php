@extends('layouts.app')

@section('title', 'Mes cagnottes - Event Global')

@section('content')
    <section class="bg-slate-900 pt-16 pb-24 px-4">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <nav class="text-xs font-bold uppercase tracking-widest text-indigo-300/80 mb-4">
                    <a href="{{ route('home') }}" class="hover:text-white">Accueil</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">Mes cagnottes</span>
                </nav>
                <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Mes cagnottes</h1>
                <p class="text-indigo-100">Suivez la progression de vos collectes et les dons reçus.</p>
            </div>
            <a href="{{ route('fundraisers.create') }}" class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-white text-indigo-600 font-black rounded-2xl hover:bg-slate-100 transition-colors">
                <i class="fa-solid fa-plus"></i> Ouvrir une cagnotte
            </a>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 -mt-12 relative z-10 pb-20">
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6">
                <i class="fa-solid fa-hand-holding-heart text-indigo-600 dark:text-indigo-400 mb-2"></i>
                <div class="text-2xl font-black">{{ $fundraisers->total() }}</div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Cagnottes</div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6">
                <i class="fa-solid fa-coins text-emerald-600 dark:text-emerald-400 mb-2"></i>
                <div class="text-2xl font-black">{{ number_format($totalRaised, 0, ',', ' ') }} FCFA</div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Collectés (page)</div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 col-span-2 lg:col-span-1">
                <i class="fa-solid fa-heart text-rose-500 mb-2"></i>
                <div class="text-2xl font-black">{{ $totalDonations }}</div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Dons reçus (page)</div>
            </div>
        </div>

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
                <h2 class="text-xl font-black mb-2">Vous n'avez encore aucune cagnotte</h2>
                <p class="text-slate-500 dark:text-slate-400 mb-8">Lancez votre première collecte en quelques minutes.</p>
                <a href="{{ route('fundraisers.create') }}" class="inline-block px-8 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700">
                    Ouvrir ma première cagnotte
                </a>
            </div>
        @endif
    </section>
@endsection
