@extends('layouts.app')

@section('title', 'Gérer les billets - '.$event->title)

@php
    $inputClass = 'w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-medium focus:ring-2 focus:ring-indigo-500 outline-none transition-colors';
    $labelClass = 'block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1';
@endphp

@section('content')
    <section class="bg-slate-900 pt-16 pb-24 px-4">
        <div class="max-w-5xl mx-auto">
            <nav class="text-xs font-bold uppercase tracking-widest text-indigo-300/80 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Accueil</a>
                <span class="mx-2">/</span>
                <a href="{{ route('events.show', $event) }}" class="hover:text-white">Évènement</a>
                <span class="mx-2">/</span>
                <span class="text-white">Billets</span>
            </nav>
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Gérer les billets</h1>
                    <p class="text-indigo-100">{{ $event->title }}</p>
                </div>
                <a href="{{ route('checkin.show', $event) }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-700 transition-colors shrink-0">
                    <i class="fa-solid fa-qrcode"></i> Check-in
                </a>
            </div>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-4 -mt-12 relative z-10 pb-20 space-y-8">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 shadow-sm">
            <h2 class="text-lg font-black mb-6 flex items-center gap-3">
                <i class="fa-solid fa-plus text-indigo-600 dark:text-indigo-400"></i>
                Ajouter une catégorie de billet
            </h2>

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/30 border-l-4 border-rose-500 text-rose-700 dark:text-rose-300 text-sm font-bold">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('ticket-types.store', $event) }}" method="POST" class="grid sm:grid-cols-12 gap-4 items-end">
                @csrf
                <div class="sm:col-span-4">
                    <label class="{{ $labelClass }}">Nom *</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="Ex : VIP" class="{{ $inputClass }}">
                </div>
                <div class="sm:col-span-3">
                    <label class="{{ $labelClass }}">Prix *</label>
                    <input type="number" name="price" min="0" step="500" required value="{{ old('price', 0) }}" class="{{ $inputClass }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}">Quantité</label>
                    <input type="number" name="quantity" min="1" value="{{ old('quantity') }}" placeholder="Illimité" class="{{ $inputClass }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}">Devise</label>
                    <select name="currency" class="{{ $inputClass }}">
                        @foreach($currencies as $code => $label)
                            <option value="{{ $code }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
                <div class="sm:col-span-12">
                    <input type="text" name="description" value="{{ old('description') }}" placeholder="Description (optionnel)" class="{{ $inputClass }}">
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 shadow-sm">
            <h2 class="text-lg font-black mb-6 flex items-center gap-3">
                <i class="fa-solid fa-list text-indigo-600 dark:text-indigo-400"></i>
                Catégories existantes
            </h2>

            @forelse($ticketTypes as $type)
                <form action="{{ route('ticket-types.update', [$event, $type]) }}" method="POST"
                      class="py-5 {{ !$loop->last ? 'border-b border-slate-100 dark:border-slate-800' : '' }}">
                    @csrf
                    @method('PUT')
                    <div class="grid sm:grid-cols-12 gap-3 items-end">
                        <div class="sm:col-span-4">
                            <label class="{{ $labelClass }}">Nom</label>
                            <input type="text" name="name" required value="{{ $type->name }}" class="{{ $inputClass }}">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="{{ $labelClass }}">Prix</label>
                            <input type="number" name="price" min="0" step="500" required value="{{ $type->price }}" class="{{ $inputClass }}">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="{{ $labelClass }}">Quantité</label>
                            <input type="number" name="quantity" min="1" value="{{ $type->quantity }}" placeholder="Illimité" class="{{ $inputClass }}">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="{{ $labelClass }}">Ordre</label>
                            <input type="number" name="sort_order" min="0" value="{{ $type->sort_order }}" class="{{ $inputClass }}">
                        </div>
                        <div class="sm:col-span-1 flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" {{ $type->is_active ? 'checked' : '' }}
                                   class="w-5 h-5 rounded-lg border-2 border-slate-300 text-indigo-600 focus:ring-4 focus:ring-indigo-500/20">
                        </div>
                        <div class="sm:col-span-12 flex flex-wrap items-center justify-between gap-3">
                            <p class="text-xs font-bold text-slate-400">
                                {{ $type->tickets_count ?? $type->tickets()->count() }} billet(s) vendu(s)
                                &middot; {{ $type->is_free ? 'Gratuit' : $type->price_label }}
                                @if($type->remaining !== null) &middot; {{ $type->remaining }} restant(s) @endif
                            </p>
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 text-sm font-black text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors">
                                    <i class="fa-solid fa-check"></i> Enregistrer
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <form action="{{ route('ticket-types.destroy', [$event, $type]) }}" method="POST" class="mt-3"
                      onsubmit="return confirm('Supprimer cette catégorie ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-black text-rose-600 hover:underline">
                        <i class="fa-solid fa-trash"></i> Supprimer « {{ $type->name }} »
                    </button>
                </form>
            @empty
                <p class="text-slate-500 dark:text-slate-400">
                    Aucune catégorie de billet. Ajoutez-en une ci-dessus pour ouvrir la billetterie.
                </p>
            @endforelse
        </div>
    </section>
@endsection
