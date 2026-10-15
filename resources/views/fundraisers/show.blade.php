@extends('layouts.app')

@section('title', $fundraiser->title.' - Cagnotte - Event Global')

@php
    $image = $fundraiser->image_url;
    $percent = $fundraiser->progress_percent;
    $daysLeft = $fundraiser->daysLeft();
@endphp

@section('content')
    <section class="relative bg-slate-900 h-[340px] md:h-[420px]">
        @if($image)
            <img src="{{ $image }}" alt="{{ $fundraiser->title }}" class="absolute inset-0 w-full h-full object-cover opacity-50">
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-700 to-violet-900 opacity-70"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-slate-900/30"></div>

        <div class="relative max-w-7xl mx-auto px-4 h-full flex flex-col justify-end pb-12">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="bg-rose-600 text-white text-xs font-black uppercase tracking-wider px-3 py-1.5 rounded-full">
                    <i class="fa-solid fa-hand-holding-heart mr-1"></i> Cagnotte
                </span>
                <span class="{{ $fundraiser->isOpen() ? 'bg-emerald-500' : 'bg-slate-600' }} text-white text-xs font-black uppercase tracking-wider px-3 py-1.5 rounded-full">
                    {{ $fundraiser->isOpen() ? 'Ouverte' : 'Clôturée' }}
                </span>
                @if($fundraiser->is_reached)
                    <span class="bg-amber-400 text-slate-900 text-xs font-black uppercase tracking-wider px-3 py-1.5 rounded-full">
                        <i class="fa-solid fa-circle-check mr-1"></i> Objectif atteint
                    </span>
                @endif
            </div>

            <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-4 leading-tight">{{ $fundraiser->title }}</h1>

            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-indigo-100 text-sm font-medium">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-user text-indigo-400"></i>
                    Lancée par {{ $fundraiser->user?->name ?? 'Event Global' }}
                </span>
                @if($daysLeft !== null)
                    <span class="flex items-center gap-2">
                        <i class="fa-regular fa-clock text-indigo-400"></i>
                        {{ $daysLeft > 0 ? 'Il reste '.$daysLeft.' jour'.($daysLeft > 1 ? 's' : '') : 'Dernier jour' }}
                    </span>
                @endif
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-users text-indigo-400"></i>
                    {{ $fundraiser->donors_count }} don{{ $fundraiser->donors_count > 1 ? 's' : '' }}
                </span>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 py-14 grid lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
                <h2 class="text-xl font-black mb-4 flex items-center gap-3">
                    <i class="fa-solid fa-circle-info text-indigo-600 dark:text-indigo-400"></i>
                    À propos de cette cagnotte
                </h2>
                <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 leading-relaxed">
                    {!! nl2br(e($fundraiser->description)) !!}
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
                <h2 class="text-xl font-black mb-6 flex items-center gap-3">
                    <i class="fa-solid fa-heart text-rose-500"></i>
                    Derniers dons
                </h2>

                @forelse($donations as $donation)
                    <div class="flex items-start gap-4 py-4 {{ ! $loop->last ? 'border-b border-slate-100 dark:border-slate-800' : '' }}">
                        <div class="w-11 h-11 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black shrink-0">
                            {{ mb_strtoupper(mb_substr($donation->donor_display, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-3">
                                <span class="font-bold text-slate-800 dark:text-slate-100 truncate">{{ $donation->donor_display }}</span>
                                <span class="font-black text-indigo-600 dark:text-indigo-400 shrink-0">{{ $donation->amount_label }}</span>
                            </div>
                            @if($donation->message)
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $donation->message }}</p>
                            @endif
                            <span class="text-xs text-slate-400 dark:text-slate-500">{{ $donation->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 dark:text-slate-400">
                        Aucun don pour le moment. Soyez le premier à soutenir ce projet !
                    </p>
                @endforelse
            </div>
        </div>

        <aside class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 sticky top-28">
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $fundraiser->raised_label }}</div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-1 mb-4">
                    collectés sur {{ $fundraiser->goal_label }}
                </p>

                <div class="w-full h-3 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full {{ $fundraiser->is_reached ? 'bg-emerald-500' : 'bg-indigo-600' }}" style="width: {{ $percent }}%"></div>
                </div>
                <div class="flex items-center justify-between mt-2 text-xs font-bold text-slate-400 dark:text-slate-500">
                    <span>{{ $percent }}%</span>
                    <span>{{ $fundraiser->donors_count }} don{{ $fundraiser->donors_count > 1 ? 's' : '' }}</span>
                </div>

                @if($canDonate)
                    @auth
                        <form action="{{ route('fundraisers.donate', $fundraiser) }}" method="POST" class="mt-8 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Montant du don ({{ $fundraiser->currency }})</label>
                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    @foreach([1000, 5000, 10000] as $preset)
                                        <button type="button" onclick="document.getElementById('donation-amount').value = {{ $preset }}"
                                                class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-black text-slate-600 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                                            {{ number_format($preset, 0, ',', ' ') }}
                                        </button>
                                    @endforeach
                                </div>
                                <input id="donation-amount" type="number" name="amount" min="500" step="500" required placeholder="Montant libre"
                                       class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none">
                            </div>
                            <textarea name="message" rows="2" placeholder="Un mot d'encouragement (optionnel)"
                                      class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                            <label class="flex items-center gap-3 text-sm font-bold text-slate-600 dark:text-slate-300 cursor-pointer">
                                <input type="checkbox" name="is_anonymous" value="1" class="w-5 h-5 rounded-lg border-2 border-slate-300 text-indigo-600 focus:ring-4 focus:ring-indigo-500/20">
                                Don anonyme
                            </label>
                            <button type="submit" class="w-full px-6 py-4 bg-rose-600 text-white font-black rounded-2xl hover:bg-rose-700 transition-colors">
                                <i class="fa-solid fa-hand-holding-dollar mr-2"></i>Donner maintenant
                            </button>
                            <p class="text-[11px] text-center text-slate-400 dark:text-slate-500">
                                Paiement simulé (démonstration) — aucune somme réelle n'est débitée.
                            </p>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="mt-8 w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-rose-600 text-white font-black rounded-2xl hover:bg-rose-700 transition-colors">
                            <i class="fa-solid fa-right-to-bracket"></i> Connectez-vous pour donner
                        </a>
                    @endauth
                @else
                    <div class="mt-8 p-4 rounded-2xl bg-slate-100 dark:bg-slate-800 text-sm font-bold text-slate-500 dark:text-slate-400 text-center">
                        <i class="fa-solid fa-lock mr-1"></i> Cette cagnotte est clôturée.
                    </div>
                @endif

                @if($fundraiser->contact_phone || $fundraiser->contact_email)
                    <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 space-y-3 text-sm">
                        @if($fundraiser->contact_phone)
                            <a href="tel:{{ preg_replace('/\s+/', '', $fundraiser->contact_phone) }}" class="flex items-center gap-3 text-slate-600 dark:text-slate-300 hover:text-indigo-600">
                                <i class="fa-solid fa-phone text-indigo-500"></i>{{ $fundraiser->contact_phone }}
                            </a>
                        @endif
                        @if($fundraiser->contact_email)
                            <a href="mailto:{{ $fundraiser->contact_email }}" class="flex items-center gap-3 text-slate-600 dark:text-slate-300 hover:text-indigo-600">
                                <i class="fa-regular fa-envelope text-indigo-500"></i>{{ $fundraiser->contact_email }}
                            </a>
                        @endif
                    </div>
                @endif

                @if($canManage)
                    <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 space-y-3">
                        <a href="{{ route('fundraisers.edit', $fundraiser) }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-2xl hover:opacity-90 transition-opacity">
                            <i class="fa-solid fa-pen"></i> Modifier
                        </a>
                        <form action="{{ route('fundraisers.destroy', $fundraiser) }}" method="POST"
                              onsubmit="return confirm('Supprimer définitivement cette cagnotte ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 font-black rounded-2xl hover:bg-rose-100 dark:hover:bg-rose-900/50 transition-colors">
                                <i class="fa-solid fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </aside>
    </section>
@endsection
