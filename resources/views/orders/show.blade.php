@extends('layouts.app')

@section('title', 'Commande '.$order->reference.' - Event Global')

@php
    $event = $order->event;
@endphp

@section('content')
    <section class="bg-slate-900 pt-16 pb-24 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl mb-6">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <span class="inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ $order->status === 'paid' ? 'bg-emerald-500' : 'bg-amber-500' }} text-white mb-4">
                {{ $order->status_label }}
            </span>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Merci pour votre commande !</h1>
            <p class="text-indigo-100">
                Commande <span class="font-black text-white">{{ $order->reference }}</span> &middot;
                {{ $order->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>
    </section>

    <section class="max-w-4xl mx-auto px-4 -mt-12 relative z-10 pb-20 space-y-8">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 mb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-1">Évènement</p>
                    <p class="font-black text-slate-800 dark:text-slate-100">{{ $event?->title ?? 'Évènement' }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        {{ $event?->starts_at?->translatedFormat('d/m/Y à H\hi') }} &middot; {{ $event?->venue }}, {{ $event?->city }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-1">Total</p>
                    <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $order->total_label }}</p>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 dark:text-slate-100">{{ $item->ticketType?->name ?? 'Billet' }}</p>
                            <p class="text-xs text-slate-400">
                                {{ $item->quantity }} × {{ number_format((float) $item->unit_price, 0, ',', ' ') }} {{ $order->currency }}
                            </p>
                        </div>
                        <span class="font-black text-slate-700 dark:text-slate-200 shrink-0">
                            {{ number_format((float) $item->subtotal, 0, ',', ' ') }} {{ $order->currency }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('tickets.index') }}"
                   class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
                    <i class="fa-solid fa-ticket"></i> Voir mes billets
                </a>
                @if($event)
                    <a href="{{ route('events.show', $event) }}"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-4 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Retour à l'évènement
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 shadow-sm">
            <h2 class="text-lg font-black mb-6 flex items-center gap-3">
                <i class="fa-solid fa-qrcode text-indigo-600 dark:text-indigo-400"></i>
                Vos billets ({{ $order->tickets->count() }})
            </h2>

            <div class="grid sm:grid-cols-2 gap-4">
                @foreach($order->tickets as $ticket)
                    <div class="rounded-2xl border border-slate-100 dark:border-slate-800 p-5 flex flex-col">
                        <a href="{{ route('tickets.show', $ticket) }}" class="block hover:opacity-90 transition-opacity">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400">{{ $ticket->ticketType?->name ?? 'Billet' }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-black {{ $ticket->status === 'valid' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $ticket->status === 'valid' ? 'Valide' : ($ticket->status === 'used' ? 'Utilisé' : 'Annulé') }}
                                </span>
                            </div>
                            <p class="font-mono font-bold text-slate-700 dark:text-slate-200 text-sm break-all">{{ $ticket->code }}</p>
                            <p class="text-xs text-slate-400 mt-2">{{ $ticket->holder() }}</p>
                        </a>
                        @if($ticket->status === 'valid')
                            <button type="button"
                                    data-qr-download
                                    data-code="{{ $ticket->code }}"
                                    data-filename="billet-{{ $ticket->code }}.png"
                                    class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-black text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                                <i class="fa-solid fa-download"></i> Télécharger le QR
                            </button>
                        @else
                            <div class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-black text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 rounded-xl">
                                <i class="fa-solid fa-ban"></i> Billet déjà utilisé
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-center">
                <button type="button"
                        data-qr-download-all
                        data-filename="commande-{{ $order->reference }}.zip"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
                    <i class="fa-solid fa-file-zipper"></i> Télécharger tous les QR codes (ZIP)
                </button>
            </div>
        </div>
    </section>
@endsection
