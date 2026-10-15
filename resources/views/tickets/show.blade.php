@extends('layouts.app')

@section('title', 'Billet '.$ticket->code.' - Event Global')

@php
    $event = $ticket->event;
    $statusClass = $ticket->status === 'valid'
        ? 'bg-emerald-500'
        : ($ticket->status === 'used' ? 'bg-slate-500' : 'bg-rose-500');
    $statusLabel = $ticket->status === 'valid' ? 'Valide' : ($ticket->status === 'used' ? 'Déjà utilisé' : 'Annulé');
@endphp

@section('content')
    <section class="max-w-lg mx-auto px-4 py-16">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-lg">
            @if($event)
                <div class="relative h-40 bg-slate-900">
                    @if($event->image_url)
                        <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="absolute inset-0 w-full h-full object-cover opacity-60">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
                    <div class="relative h-full flex flex-col justify-end p-6">
                        <span class="self-start px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider text-white {{ $statusClass }}">{{ $statusLabel }}</span>
                        <h1 class="text-xl font-extrabold text-white mt-2 leading-snug">{{ $event->title }}</h1>
                    </div>
                </div>
            @endif

            <div class="p-8">
                <div class="text-center">
                    <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4">Présentez ce QR code à l'entrée</p>

                    <div class="inline-block p-5 bg-white rounded-2xl border border-slate-200">
                        <canvas id="ticket-qr" width="320" height="320" class="mx-auto w-56 h-56"></canvas>
                    </div>

                    <div class="mt-6 font-mono font-black text-lg text-slate-800 dark:text-slate-100 break-all">{{ $ticket->code }}</div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $ticket->holder() }}</p>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-5 text-sm">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-1">Catégorie</p>
                        <p class="font-bold text-slate-800 dark:text-slate-100">{{ $ticket->ticketType?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-1">Tarif</p>
                        <p class="font-bold text-slate-800 dark:text-slate-100">{{ $ticket->ticketType?->price_label ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-1">Date</p>
                        <p class="font-bold text-slate-800 dark:text-slate-100">{{ $event?->starts_at?->translatedFormat('d/m/Y à H\hi') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-1">Lieu</p>
                        <p class="font-bold text-slate-800 dark:text-slate-100">{{ $event?->venue ?? '—' }}</p>
                    </div>
                    @if($ticket->status === 'used')
                        <div class="col-span-2">
                            <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-1">Scanné le</p>
                            <p class="font-bold text-rose-600 dark:text-rose-400">{{ $ticket->checked_in_at?->format('d/m/Y à H:i') }}</p>
                        </div>
                    @endif
                </div>

                <div class="mt-8 space-y-3">
                    <button type="button"
                            data-qr-download
                            data-code="{{ $ticket->code }}"
                            data-filename="billet-{{ $ticket->code }}.png"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-700 transition-colors">
                        <i class="fa-solid fa-download"></i> Télécharger le QR code
                    </button>
                    @if($event)
                        <a href="{{ route('events.show', $event) }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
                            <i class="fa-solid fa-calendar"></i> Voir l'évènement
                        </a>
                    @endif
                    <a href="{{ route('tickets.index') }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-arrow-left"></i> Mes billets
                    </a>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            (function () {
                const code = @json($ticket->code);
                const canvas = document.getElementById('ticket-qr');
                if (!canvas) return;

                function drawFallback() {
                    const ctx = canvas.getContext('2d');
                    ctx.fillStyle = '#0f172a';
                    ctx.font = 'bold 20px monospace';
                    ctx.textAlign = 'center';
                    ctx.fillText(code, canvas.width / 2, canvas.height / 2);
                }

                function render() {
                    if (window.QRCode && typeof window.QRCode.toCanvas === 'function') {
                        window.QRCode.toCanvas(canvas, code, {
                            width: 320,
                            margin: 2,
                            color: { dark: '#0f172a', light: '#ffffff' },
                            errorCorrectionLevel: 'M',
                        }, function (err) {
                            if (err) drawFallback();
                        });
                        return;
                    }
                    fallbackAttempts++;
                    if (fallbackAttempts < 50) {
                        window.setTimeout(render, 100);
                        return;
                    }
                    drawFallback();
                }

                let fallbackAttempts = 0;

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', render);
                } else {
                    render();
                }
            })();
        </script>
    @endpush
@endsection
