@extends('layouts.app')

@section('title', 'Vote - '.$event->title.' - Event Global')

@section('content')
    <section class="bg-slate-900 pt-16 pb-24 px-4">
        <div class="max-w-6xl mx-auto">
            <nav class="text-xs font-bold uppercase tracking-widest text-indigo-300/80 mb-4">
                <a href="{{ route('events.index') }}" class="hover:text-white">Évènements</a>
                <span class="mx-2">/</span>
                <a href="{{ route('events.show', $event) }}" class="hover:text-white">Compétition</a>
                <span class="mx-2">/</span>
                <span class="text-white">Vote</span>
            </nav>

            <span class="inline-flex items-center gap-2 bg-amber-500 text-white text-xs font-black uppercase tracking-wider px-3 py-1.5 rounded-full mb-4">
                <i class="fa-solid fa-trophy"></i> Compétition
            </span>

            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">{{ $event->title }}</h1>
            <p class="text-indigo-100 max-w-3xl">
                Vote pour ton candidat favori : un vote <strong class="text-emerald-300">pour</strong> (+1) et un vote
                <strong class="text-rose-300">contre</strong> (-1) par personne. Clique à nouveau sur le même bouton pour annuler ton vote.
            </p>

            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-indigo-100 text-sm font-medium mt-5">
                <span class="flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-indigo-400"></i>
                    {{ $event->starts_at->translatedFormat('l d F Y') }}
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-indigo-400"></i>
                    {{ $event->venue }}, {{ $event->city }}
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-users text-indigo-400"></i>
                    {{ $candidates->count() }} candidat{{ $candidates->count() > 1 ? 's' : '' }}
                </span>
            </div>

            @if($event->isPast())
                <div class="mt-6 inline-flex items-center gap-2 bg-slate-700 text-white text-sm font-bold px-4 py-2 rounded-2xl">
                    <i class="fa-solid fa-lock"></i> Cette compétition est terminée : les votes sont clos.
                </div>
            @endif
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 -mt-12 relative z-10 pb-20 grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            @if($candidates->count())
                <div class="space-y-5">
                    @foreach($candidates as $candidate)
                        @if($candidate->is_active || $canManage)
                            @include('competitions.partials.candidate-card', [
                                'candidate' => $candidate,
                                'rank' => $loop->index + 1,
                            ])
                        @endif
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-14 text-center">
                    <i class="fa-solid fa-user-plus text-5xl text-slate-300 dark:text-slate-600 mb-6"></i>
                    <h2 class="text-xl font-black mb-2">Aucun candidat pour le moment</h2>
                    <p class="text-slate-500 dark:text-slate-400">
                        {{ $canManage ? 'Ajoutez le premier candidat avec le formulaire à droite.' : 'Les candidats seront affichés ici dès leur inscription.' }}
                    </p>
                </div>
            @endif
        </div>

        <aside class="space-y-6">
            @if($canManage)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
                    <h2 class="text-lg font-black mb-6 flex items-center gap-3">
                        <i class="fa-solid fa-plus text-indigo-600 dark:text-indigo-400"></i>
                        Ajouter un candidat
                    </h2>
                    @include('competitions.partials.form', [
                        'action' => route('competitions.candidates.store', $event),
                        'method' => 'POST',
                        'candidate' => null,
                        'submitLabel' => 'Ajouter le candidat',
                    ])
                </div>
            @endif

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
                <h2 class="text-lg font-black mb-5 flex items-center gap-3">
                    <i class="fa-solid fa-ranking-star text-indigo-600 dark:text-indigo-400"></i>
                    Classement
                </h2>
                <ol class="space-y-3">
                    @forelse($candidates->take(5) as $candidate)
                        <li class="flex items-center gap-3 text-sm">
                            <span class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center text-xs font-black shrink-0">
                                {{ $loop->iteration }}
                            </span>
                            <span class="font-bold truncate flex-1">{{ $candidate->name }}</span>
                            <span class="font-black {{ $candidate->score >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $candidate->score }}
                            </span>
                        </li>
                    @empty
                        <li class="text-sm text-slate-400 dark:text-slate-500">Pas encore de candidat.</li>
                    @endforelse
                </ol>
            </div>

            <div class="bg-slate-900 rounded-3xl p-8 text-white">
                <h2 class="text-lg font-black mb-4 flex items-center gap-3">
                    <i class="fa-solid fa-circle-info text-indigo-400"></i>
                    Règles du vote
                </h2>
                <ul class="space-y-3 text-sm text-indigo-100/80">
                    <li class="flex gap-3"><i class="fa-solid fa-thumbs-up text-emerald-400 mt-1"></i> Un vote « pour » ajoute +1 au score du candidat.</li>
                    <li class="flex gap-3"><i class="fa-solid fa-thumbs-down text-rose-400 mt-1"></i> Un vote « contre » retire -1 au score du candidat.</li>
                    <li class="flex gap-3"><i class="fa-solid fa-rotate-left text-indigo-300 mt-1"></i> Recliquez sur le même bouton pour annuler votre vote.</li>
                    <li class="flex gap-3"><i class="fa-solid fa-user-shield text-amber-300 mt-1"></i> Un compte par personne pour éviter la triche.</li>
                </ul>
            </div>
        </aside>
    </section>
@endsection
