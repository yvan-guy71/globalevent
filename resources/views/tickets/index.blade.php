@extends('layouts.app')

@section('title', 'Mes billets - Event Global')

@section('content')
    <section class="bg-slate-900 pt-16 pb-24 px-4">
        <div class="max-w-6xl mx-auto">
            <nav class="text-xs font-bold uppercase tracking-widest text-indigo-300/80 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Accueil</a>
                <span class="mx-2">/</span>
                <span class="text-white">Mes billets</span>
            </nav>
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Mes billets</h1>
                    <p class="text-indigo-100">Vos entrées, avec QR code pour un check-in rapide.</p>
                </div>
                <a href="{{ route('orders.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-indigo-600 font-black rounded-2xl hover:bg-slate-100 transition-colors shrink-0">
                    <i class="fa-solid fa-receipt"></i> Mes commandes
                </a>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 -mt-12 relative z-10 pb-20">
        @if($tickets->count())
            @php $groupedTickets = $tickets->groupBy('event_id'); @endphp
            <div class="space-y-10">
                @foreach($groupedTickets as $eventId => $eventTickets)
                    @php $event = $eventTickets->first()->event; @endphp
                    <div data-qr-scope class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <h2 class="text-xl font-black text-slate-900 dark:text-white">
                                {{ $event?->title ?? 'Évènement supprimé' }}
                            </h2>
                            <button type="button"
                                    data-qr-download-all
                                    data-filename="billets-{{ $event?->slug ?? $eventId }}.zip"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 transition-colors text-sm">
                                <i class="fa-solid fa-file-zipper"></i> Télécharger ce groupe (ZIP)
                            </button>
                        </div>

                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($eventTickets as $ticket)
                                @php
                                    $statusClass = $ticket->status === 'valid'
                                        ? 'bg-emerald-500'
                                        : ($ticket->status === 'used' ? 'bg-slate-500' : 'bg-rose-500');
                                    $statusLabel = $ticket->status === 'valid' ? 'Valide' : ($ticket->status === 'used' ? 'Utilisé' : 'Annulé');
                                @endphp

                                <div
                                   class="group bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow flex flex-col">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="flex flex-col flex-1">
                                        <div class="relative h-32 bg-slate-900">
                                            @if($event?->image_url)
                                                <img src="{{ $event->image_url }}" alt="{{ $event?->title }}" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-300">
                                            @endif
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
                                            <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider text-white {{ $statusClass }}">{{ $statusLabel }}</span>
                                        </div>

                                        <div class="p-5 flex-1 flex flex-col">
                                            <p class="font-black text-slate-800 dark:text-slate-100 leading-snug line-clamp-2">{{ $event?->title ?? 'Évènement supprimé' }}</p>
                                            <p class="text-xs text-slate-400 mt-1">
                                                {{ $event?->starts_at?->translatedFormat('d/m/Y à H\hi') ?? '—' }}
                                            </p>

                                            <div class="mt-auto pt-4 flex items-center justify-between gap-3">
                                                <span class="text-xs font-black text-indigo-600 dark:text-indigo-400 truncate">{{ $ticket->ticketType?->name ?? 'Billet' }}</span>
                                                <span class="font-mono text-xs font-bold text-slate-400 truncate">{{ $ticket->code }}</span>
                                            </div>
                                        </div>
                                    </a>

                                    <div class="px-5 pb-5 pt-1">
                                        <button type="button"
                                                data-qr-download
                                                data-code="{{ $ticket->code }}"
                                                data-filename="billet-{{ $ticket->code }}.png"
                                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-black text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                                            <i class="fa-solid fa-download"></i> Télécharger le QR
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-center">
                <button type="button"
                        data-qr-download-all
                        data-filename="mes-billets.zip"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
                    <i class="fa-solid fa-file-zipper"></i> Télécharger tous les QR codes (ZIP)
                </button>
            </div>

            <div class="mt-10">
                {{ $tickets->links() }}
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-16 text-center">
                <i class="fa-solid fa-ticket text-5xl text-slate-300 dark:text-slate-600 mb-6"></i>
                <h2 class="text-xl font-black mb-2">Vous n'avez encore aucun billet</h2>
                <p class="text-slate-500 dark:text-slate-400 mb-8">Réservez votre place pour recevoir vos billets avec QR code.</p>
                <a href="{{ route('events.index') }}" class="inline-block px-8 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700">
                    Découvrir les évènements
                </a>
            </div>
        @endif
    </section>
@endsection
