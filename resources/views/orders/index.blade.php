@extends('layouts.app')

@section('title', 'Mes commandes - Event Global')

@section('content')
    <section class="bg-slate-900 pt-16 pb-24 px-4">
        <div class="max-w-5xl mx-auto">
            <nav class="text-xs font-bold uppercase tracking-widest text-indigo-300/80 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Accueil</a>
                <span class="mx-2">/</span>
                <span class="text-white">Mes commandes</span>
            </nav>
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Mes commandes</h1>
                    <p class="text-indigo-100">Retrouvez vos réservations et leurs billets.</p>
                </div>
                <a href="{{ route('tickets.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-indigo-600 font-black rounded-2xl hover:bg-slate-100 transition-colors shrink-0">
                    <i class="fa-solid fa-ticket"></i> Mes billets
                </a>
            </div>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-4 -mt-12 relative z-10 pb-20">
        @if($orders->count())
            <div class="space-y-5">
                @foreach($orders as $order)
                    <a href="{{ route('orders.show', $order) }}"
                       class="block bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="min-w-0">
                                <p class="font-black text-slate-800 dark:text-slate-100 truncate">{{ $order->event?->title ?? 'Évènement supprimé' }}</p>
                                <p class="text-xs font-bold text-slate-400 mt-1">
                                    Commande {{ $order->reference }} &middot; {{ $order->created_at->format('d/m/Y H:i') }}
                                    &middot; {{ $order->tickets_count }} billet(s)
                                </p>
                            </div>
                            <div class="flex items-center gap-4 shrink-0">
                                <span class="px-3 py-1 rounded-full text-xs font-black
                                    {{ $order->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($order->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ $order->status_label }}
                                </span>
                                <span class="font-black text-indigo-600 dark:text-indigo-400">{{ $order->total_label }}</span>
                                <i class="fa-solid fa-chevron-right text-slate-300"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $orders->links() }}
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-16 text-center">
                <i class="fa-solid fa-receipt text-5xl text-slate-300 dark:text-slate-600 mb-6"></i>
                <h2 class="text-xl font-black mb-2">Aucune commande pour l'instant</h2>
                <p class="text-slate-500 dark:text-slate-400 mb-8">Réservez votre première place sur un évènement.</p>
                <a href="{{ route('events.index') }}" class="inline-block px-8 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700">
                    Découvrir les évènements
                </a>
            </div>
        @endif
    </section>
@endsection
