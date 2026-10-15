@extends('layouts.app')

@section('title', 'Mes évènements - Event Global')

@section('content')
    <section class="bg-slate-900 pt-16 pb-24 px-4">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <nav class="text-xs font-bold uppercase tracking-widest text-indigo-300/80 mb-4">
                    <a href="{{ route('home') }}" class="hover:text-white">Accueil</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">Mes évènements</span>
                </nav>
                <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Mes évènements</h1>
                <p class="text-indigo-100">
                    {{ auth()->user()->isAdmin() ? 'En tant qu\'administrateur, vous voyez tous les évènements de la plateforme.' : 'Gérez les évènements que vous avez publiés.' }}
                </p>
            </div>
            <a href="{{ route('events.create') }}" class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
                <i class="fa-solid fa-plus"></i> Créer un évènement
            </a>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 -mt-12 relative z-10 pb-20">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-strong">
                <div class="flex items-center justify-between">
                    <div class="text-3xl font-black">{{ $stats['events'] }}</div>
                    <i class="fa-regular fa-calendar text-2xl text-indigo-500"></i>
                </div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-2">
                    Évènements <span class="text-emerald-500">({{ $stats['published'] }} publiés)</span>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-strong">
                <div class="flex items-center justify-between">
                    <div class="text-3xl font-black">{{ $stats['tickets'] }}</div>
                    <i class="fa-solid fa-ticket text-2xl text-indigo-500"></i>
                </div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-2">Billets vendus</div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-strong">
                <div class="flex items-center justify-between">
                    <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['checked_in'] }}</div>
                    <i class="fa-solid fa-qrcode text-2xl text-emerald-500"></i>
                </div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-2">Entrées scannées</div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-strong">
                <div class="flex items-center justify-between">
                    <div class="text-2xl font-black">{{ number_format($stats['revenue'], 0, ',', ' ') }}</div>
                    <i class="fa-solid fa-coins text-2xl text-amber-500"></i>
                </div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-2">Recettes (FCFA)</div>
            </div>
        </div>

        @if($events->count())
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-strong">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                <th class="px-6 py-4">Évènement</th>
                                <th class="px-6 py-4 hidden md:table-cell">Date</th>
                                <th class="px-6 py-4 hidden lg:table-cell">Lieu</th>
                                <th class="px-6 py-4">Statut</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($events as $event)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <img src="{{ $event->image_url ?: asset($event->fallback_image) }}" alt=""
                                                 class="w-14 h-14 rounded-xl object-cover shrink-0">
                                            <div class="min-w-0">
                                                <a href="{{ route('events.show', $event) }}" class="font-bold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 line-clamp-1">
                                                    {{ $event->title }}
                                                </a>
                                                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">{{ $event->type_label }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-300">
                                        {{ $event->starts_at->translatedFormat('d M Y - H\hi') }}
                                    </td>
                                    <td class="px-6 py-4 hidden lg:table-cell text-sm text-slate-600 dark:text-slate-300">
                                        {{ $event->city }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusStyles = [
                                                'published' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                                'draft' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                                'cancelled' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
                                            ];
                                        @endphp
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-black {{ $statusStyles[$event->status] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ $event->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('checkin.show', $event) }}" title="Scanner les billets"
                                               class="w-9 h-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30">
                                                <i class="fa-solid fa-qrcode text-sm"></i>
                                            </a>
                                            <a href="{{ route('events.show', $event) }}" title="Voir"
                                               class="w-9 h-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                                                <i class="fa-regular fa-eye text-sm"></i>
                                            </a>
                                            <a href="{{ route('events.edit', $event) }}" title="Modifier"
                                               class="w-9 h-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30">
                                                <i class="fa-solid fa-pen text-sm"></i>
                                            </a>
                                            <form action="{{ route('events.destroy', $event) }}" method="POST"
                                                  onsubmit="return confirm('Supprimer définitivement « {{ $event->title }} » ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Supprimer"
                                                        class="w-9 h-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30">
                                                    <i class="fa-solid fa-trash text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-10">
                {{ $events->links() }}
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-16 text-center">
                <i class="fa-regular fa-calendar-plus text-5xl text-slate-300 dark:text-slate-600 mb-6"></i>
                <h2 class="text-xl font-black mb-2">Vous n'avez encore rien publié</h2>
                <p class="text-slate-500 dark:text-slate-400 mb-8">Créez votre premier évènement et partagez-le avec votre public.</p>
                <a href="{{ route('events.create') }}" class="inline-block px-8 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700">
                    Créer mon premier évènement
                </a>
            </div>
        @endif
    </section>
@endsection
