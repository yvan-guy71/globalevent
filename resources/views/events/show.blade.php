@extends('layouts.app')

@section('title', $event->title.' - Event Global')

@php
    $typeStyles = [
        'concert' => 'bg-indigo-600',
        'festival' => 'bg-violet-600',
        'competition' => 'bg-amber-500',
        'sport' => 'bg-emerald-600',
        'conference' => 'bg-blue-600',
        'spectacle' => 'bg-pink-600',
        'exposition' => 'bg-orange-500',
        'soiree' => 'bg-rose-600',
        'formation' => 'bg-cyan-600',
        'autre' => 'bg-slate-600',
    ];
    $badge = $typeStyles[$event->type] ?? 'bg-slate-600';
    $image = $event->image_url ?: asset($event->fallback_image);
    $canManage = auth()->check() && (auth()->id() === $event->user_id || auth()->user()->isAdmin());
@endphp

@section('content')
    <section class="relative bg-slate-900 h-[380px] md:h-[460px]">
        <img src="{{ $image }}" alt="{{ $event->title }}" class="absolute inset-0 w-full h-full object-cover opacity-50">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-slate-900/30"></div>

        <div class="relative max-w-7xl mx-auto px-4 h-full flex flex-col justify-end pb-12">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="{{ $badge }} text-white text-xs font-black uppercase tracking-wider px-3 py-1.5 rounded-full">
                    {{ $event->type_label }}
                </span>
                <span class="{{ $event->is_free ? 'bg-emerald-500' : 'bg-white text-slate-900' }} text-xs font-black uppercase tracking-wider px-3 py-1.5 rounded-full">
                    {{ $event->price_label }}
                </span>
                @if($event->status !== 'published')
                    <span class="bg-amber-400 text-slate-900 text-xs font-black uppercase tracking-wider px-3 py-1.5 rounded-full">
                        {{ $event->status_label }}
                    </span>
                @endif
                @if($event->isPast())
                    <span class="bg-slate-700 text-white text-xs font-black uppercase tracking-wider px-3 py-1.5 rounded-full">Terminé</span>
                @endif
            </div>

            <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-4 leading-tight">{{ $event->title }}</h1>

            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-indigo-100 text-sm font-medium">
                <span class="flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-indigo-400"></i>
                    {{ $event->starts_at->translatedFormat('l d F Y à H\hi') }}
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-indigo-400"></i>
                    {{ $event->venue }}, {{ $event->city }}
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-user-group text-indigo-400"></i>
                    Organisé par {{ $event->user?->name ?? 'Event Global' }}
                </span>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 py-14 grid lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
                <h2 class="text-xl font-black mb-4 flex items-center gap-3">
                    <i class="fa-solid fa-circle-info text-indigo-600 dark:text-indigo-400"></i>
                    À propos de cet évènement
                </h2>
                <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 leading-relaxed">
                    {!! nl2br(e($event->description)) !!}
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
                <h2 class="text-xl font-black mb-6 flex items-center gap-3">
                    <i class="fa-solid fa-list-check text-indigo-600 dark:text-indigo-400"></i>
                    Informations pratiques
                </h2>

                @php
                    $details = [
                        ['icon' => 'fa-calendar-day', 'label' => 'Début', 'value' => $event->starts_at->translatedFormat('d/m/Y à H\hi')],
                        ['icon' => 'fa-calendar-check', 'label' => 'Fin', 'value' => $event->ends_at ? $event->ends_at->translatedFormat('d/m/Y à H\hi') : 'Non précisée'],
                        ['icon' => 'fa-location-dot', 'label' => 'Lieu', 'value' => $event->venue.($event->address ? ' — '.$event->address : '')],
                        ['icon' => 'fa-city', 'label' => 'Ville', 'value' => $event->city],
                        ['icon' => 'fa-globe', 'label' => 'Pays', 'value' => $event->country],
                        ['icon' => 'fa-ticket', 'label' => 'Tarif', 'value' => $event->price_label],
                        ['icon' => 'fa-user-group', 'label' => 'Capacité', 'value' => $event->capacity ? number_format($event->capacity, 0, ',', ' ').' places' : 'Non limitée'],
                    ];
                @endphp

                <dl class="grid sm:grid-cols-2 gap-6">
                    @foreach($details as $detail)
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                <i class="fa-solid {{ $detail['icon'] }}"></i>
                            </div>
                            <div>
                                <dt class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ $detail['label'] }}</dt>
                                <dd class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $detail['value'] }}</dd>
                            </div>
                        </div>
                    @endforeach
                </dl>
            </div>

            @if($event->isCompetition())
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <h2 class="text-xl font-black flex items-center gap-3">
                            <i class="fa-solid fa-trophy text-amber-500"></i>
                            Candidats & votes
                        </h2>
                        <a href="{{ route('competitions.index', $event) }}"
                           class="inline-flex items-center gap-2 px-5 py-3 bg-amber-500 text-white font-black rounded-2xl hover:bg-amber-600 transition-colors text-sm">
                            <i class="fa-solid fa-check-to-slot"></i>
                            Participer au vote
                        </a>
                    </div>

                    @if($topCandidates->count())
                        <ol class="space-y-4">
                            @foreach($topCandidates as $candidate)
                                <li class="flex items-center gap-4">
                                    <span class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 flex items-center justify-center text-sm font-black shrink-0">
                                        {{ $loop->iteration }}
                                    </span>
                                    @if($candidate->image_url)
                                        <img src="{{ $candidate->image_url }}" alt="{{ $candidate->name }}" class="w-11 h-11 rounded-xl object-cover">
                                    @else
                                        <span class="w-11 h-11 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black">
                                            {{ mb_strtoupper(mb_substr($candidate->name, 0, 1)) }}
                                        </span>
                                    @endif
                                    <span class="flex-1 font-bold truncate">{{ $candidate->name }}</span>
                                    <span class="font-black {{ $candidate->score >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ $candidate->score }} <span class="text-xs font-bold uppercase text-slate-400">voix</span>
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-500 mt-5">
                            {{ $candidatesCount }} candidat{{ $candidatesCount > 1 ? 's' : '' }} en compétition &middot;
                            Vote pour (+1) ou contre (-1), un vote par personne.
                        </p>
                    @else
                        <p class="text-slate-500 dark:text-slate-400">
                            Les candidats de cette compétition seront bientôt dévoilés. Revenez voter !
                        </p>
                    @endif
                </div>
            @endif

            @if($ticketTypes->count())
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <h2 class="text-xl font-black flex items-center gap-3">
                            <i class="fa-solid fa-ticket text-indigo-600 dark:text-indigo-400"></i>
                            Billetterie
                        </h2>
                        @unless($event->isPast())
                            <a href="{{ route('orders.create', $event) }}"
                               class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors text-sm">
                                <i class="fa-solid fa-cart-shopping"></i>
                                Réserver
                            </a>
                        @endunless
                    </div>

                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($ticketTypes as $type)
                            <li class="py-4 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="font-black text-slate-800 dark:text-slate-100">{{ $type->name }}</p>
                                    @if($type->description)
                                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $type->description }}</p>
                                    @endif
                                    @if($type->remaining !== null)
                                        <span class="inline-block mt-1 text-xs font-bold {{ $type->is_sold_out ? 'text-rose-500' : 'text-emerald-600 dark:text-emerald-400' }}">
                                            {{ $type->is_sold_out ? 'Épuisé' : $type->remaining.' restant'.($type->remaining > 1 ? 's' : '') }}
                                        </span>
                                    @else
                                        <span class="inline-block mt-1 text-xs font-bold text-slate-400">Illimité</span>
                                    @endif
                                </div>
                                <span class="font-black text-indigo-600 dark:text-indigo-400 shrink-0">{{ $type->price_label }}</span>
                            </li>
                        @endforeach
                    </ul>

                    @if($canManage)
                        <div class="pt-6 mt-2 border-t border-slate-100 dark:border-slate-800 grid sm:grid-cols-2 gap-3">
                            <a href="{{ route('ticket-types.index', $event) }}"
                               class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-2xl hover:opacity-90 transition-opacity text-sm">
                                <i class="fa-solid fa-cog"></i> Gérer les billets
                            </a>
                            <a href="{{ route('checkin.show', $event) }}"
                               class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-700 transition-colors text-sm">
                                <i class="fa-solid fa-qrcode"></i> Check-in / Scanner
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            @if($related->count())
                <div>
                    <h2 class="text-2xl font-black mb-6">Vous aimerez aussi</h2>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($related as $item)
                            @include('events.partials.card', ['event' => $item])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <aside class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 sticky top-28">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-2xl mb-4">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $event->price_label }}</div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-1">
                        @if($event->capacity)
                            {{ $event->remaining_places }} place{{ $event->remaining_places > 1 ? 's' : '' }} disponible{{ $event->remaining_places > 1 ? 's' : '' }}
                        @else
                            Entrée libre
                        @endif
                    </p>
                </div>

                <div class="mt-8 space-y-3">
                    @if($event->is_free && !$event->isPast() && auth()->check())
                        <form action="{{ route('events.join', $event) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-700 transition-colors">
                                <i class="fa-solid fa-user-plus"></i>
                                Rejoindre l'évènement
                            </button>
                        </form>
                    @elseif($event->is_free && !$event->isPast())
                        <a href="{{ route('login') }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-700 transition-colors">
                            <i class="fa-solid fa-user-plus"></i>
                            Se connecter pour rejoindre
                        </a>
                    @elseif($ticketTypes->count() && !$event->isPast())
                        <a href="{{ route('orders.create', $event) }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
                            <i class="fa-solid fa-cart-shopping"></i>
                            Réserver ma place
                        </a>
                    @endif

                    @if($event->isCompetition())
                        <a href="{{ route('competitions.index', $event) }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-amber-500 text-white font-black rounded-2xl hover:bg-amber-600 transition-colors">
                            <i class="fa-solid fa-check-to-slot"></i>
                            Participer au vote
                        </a>
                    @endif

                    @if($event->contact_phone || $event->contact_email)
                        <a href="{{ $event->contact_phone ? 'tel:'.preg_replace('/\s+/', '', $event->contact_phone) : 'mailto:'.$event->contact_email }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
                            <i class="fa-solid fa-phone"></i>
                            Contacter l'organisateur
                        </a>
                    @endif

                    <button type="button" onclick="shareEvent()"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-share-nodes"></i>
                        Partager l'évènement
                    </button>

                    @if($canManage)
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                            <a href="{{ route('events.edit', $event) }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-2xl hover:opacity-90 transition-opacity">
                                <i class="fa-solid fa-pen"></i>
                                Modifier
                            </a>
                            <form action="{{ route('events.destroy', $event) }}" method="POST"
                                  onsubmit="return confirm('Supprimer définitivement cet évènement ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 font-black rounded-2xl hover:bg-rose-100 dark:hover:bg-rose-900/50 transition-colors">
                                    <i class="fa-solid fa-trash"></i>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                @if($event->contact_name || $event->contact_email || $event->contact_phone)
                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-sm">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Organisateur</h3>
                        @if($event->contact_name)
                            <p class="font-bold text-slate-800 dark:text-slate-100">{{ $event->contact_name }}</p>
                        @endif
                        @if($event->contact_email)
                            <p class="text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-2">
                                <i class="fa-regular fa-envelope text-indigo-500"></i>{{ $event->contact_email }}
                            </p>
                        @endif
                        @if($event->contact_phone)
                            <p class="text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-2">
                                <i class="fa-solid fa-phone text-indigo-500"></i>{{ $event->contact_phone }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </aside>
    </section>

    @push('scripts')
        <script>
            function shareEvent() {
                const data = {
                    title: @json($event->title),
                    text: @json('Découvrez '.$event->title.' sur Event Global'),
                    url: window.location.href,
                };

                if (navigator.share) {
                    navigator.share(data).catch(() => {});
                    return;
                }

                navigator.clipboard.writeText(data.url).then(() => {
                    alert('Lien de l\'évènement copié dans le presse-papier !');
                });
            }
        </script>
    @endpush
@endsection
