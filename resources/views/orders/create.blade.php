@extends('layouts.app')

@section('title', 'Réserver - '.$event->title)

@php
    $image = $event->image_url ?: asset($event->fallback_image);
    $maxGlobal = 20;
@endphp

@section('content')
    <section class="bg-slate-900 pt-16 pb-24 px-4">
        <div class="max-w-4xl mx-auto">
            <nav class="text-xs font-bold uppercase tracking-widest text-indigo-300/80 mb-4">
                <a href="{{ route('events.show', $event) }}" class="hover:text-white">Évènement</a>
                <span class="mx-2">/</span>
                <span class="text-white">Réserver</span>
            </nav>
            <div class="flex items-start gap-5">
                <img src="{{ $image }}" alt="{{ $event->title }}" class="w-24 h-24 rounded-2xl object-cover shrink-0 hidden sm:block">
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Réserver vos billets</h1>
                    <p class="text-indigo-100">{{ $event->title }}</p>
                    <p class="text-sm text-indigo-200/70 mt-1">
                        {{ $event->starts_at->translatedFormat('d/m/Y à H\hi') }} &middot; {{ $event->venue }}, {{ $event->city }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-4xl mx-auto px-4 -mt-12 relative z-10 pb-20">
        <form action="{{ route('orders.store', $event) }}" method="POST" id="checkout-form" class="space-y-6">
            @csrf

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/30 border-l-4 border-rose-500 text-rose-700 dark:text-rose-300 text-sm font-bold">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
                <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-lg font-black flex items-center gap-3">
                        <i class="fa-solid fa-ticket text-indigo-600 dark:text-indigo-400"></i>
                        Choisissez vos places
                    </h2>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($ticketTypes as $type)
                        @php
                            $limit = $type->is_sold_out ? 0 : min($maxGlobal, $type->remaining ?? $maxGlobal);
                        @endphp
                        <div class="px-8 py-6 flex flex-col sm:flex-row sm:items-center gap-4 {{ $type->is_sold_out ? 'opacity-60' : '' }}">
                            <div class="flex-1 min-w-0">
                                <p class="font-black text-slate-800 dark:text-slate-100">{{ $type->name }}</p>
                                @if($type->description)
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $type->description }}</p>
                                @endif
                                <p class="text-xs font-bold text-slate-400 mt-1">
                                    @if($type->is_sold_out)
                                        Épuisé
                                    @elseif($type->remaining !== null)
                                        {{ $type->remaining }} restant(s)
                                    @else
                                        Illimité
                                    @endif
                                </p>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="block font-black text-indigo-600 dark:text-indigo-400 text-lg" data-price="{{ $type->price }}">{{ $type->price_label }}</span>
                            </div>

                            <div class="shrink-0">
                                @if($type->is_sold_out)
                                    <span class="inline-block px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-black text-slate-400">Épuisé</span>
                                @else
                                    <input type="number" name="items[{{ $type->id }}]" value="0" min="0" max="{{ $limit }}" data-limit="{{ $limit }}"
                                           class="w-24 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold text-center focus:ring-2 focus:ring-indigo-500 outline-none qty-input">
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Total</span>
                    <span id="checkout-total" class="text-3xl font-black text-slate-900 dark:text-white">0 {{ $event->currency ?: 'FCFA' }}</span>
                </div>

                <button type="submit"
                        class="w-full px-6 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors disabled:opacity-50"
                        id="checkout-btn" disabled>
                    <i class="fa-solid fa-lock mr-2"></i> Payer (paiement simulé)
                </button>

                <p class="text-xs text-center text-slate-400 dark:text-slate-500 mt-4">
                    Paiement simulé : aucune somme réelle n'est débitée. Les billets sont générés immédiatement avec un QR code.
                </p>
            </div>
        </form>
    </section>

    @push('scripts')
        <script>
            (function () {
                const form = document.getElementById('checkout-form');
                const inputs = form.querySelectorAll('.qty-input');
                const totalEl = document.getElementById('checkout-total');
                const btn = document.getElementById('checkout-btn');
                const currency = @json($event->currency ?: 'FCFA');

                function format(n) {
                    return new Intl.NumberFormat('fr-FR').format(Math.round(n));
                }

                function update() {
                    let total = 0;
                    let count = 0;

                    inputs.forEach(input => {
                        let qty = parseInt(input.value, 10) || 0;
                        const limit = parseInt(input.dataset.limit, 10) || 20;
                        if (qty > limit) {
                            qty = limit;
                            input.value = limit;
                        }
                        if (qty < 0) qty = 0;

                        count += qty;
                        const price = parseFloat(input.closest('.flex').querySelector('[data-price]')?.dataset.price) || 0;
                        total += price * qty;
                    });

                    totalEl.textContent = format(total) + ' ' + currency;
                    btn.disabled = count === 0;
                }

                inputs.forEach(input => input.addEventListener('input', update));
                update();
            })();
        </script>
    @endpush
@endsection
