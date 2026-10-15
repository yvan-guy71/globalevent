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
    $isPast = $event->isPast();
@endphp

<article class="group bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-strong transition-all duration-300 flex flex-col h-full {{ $isPast ? 'opacity-80' : '' }}">
    <a href="{{ route('events.show', $event) }}" class="relative block h-48 overflow-hidden">
        <img src="{{ $image }}" alt="{{ $event->title }}" loading="lazy"
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

        <span class="absolute top-4 left-4 {{ $badge }} text-white text-[11px] font-black uppercase tracking-wider px-3 py-1.5 rounded-full shadow-lg">
            {{ $event->type_label }}
        </span>

        <span class="absolute top-4 right-4 {{ $event->is_free ? 'bg-emerald-500' : 'bg-white text-slate-900' }} text-[11px] font-black uppercase tracking-wider px-3 py-1.5 rounded-full shadow-lg">
            {{ $event->is_free ? 'Gratuit' : $event->price_label }}
        </span>

        @if($isPast)
            <span class="absolute bottom-4 right-4 bg-slate-900/80 text-white text-[11px] font-bold uppercase px-3 py-1 rounded-full">
                Terminé
            </span>
        @endif

        <div class="absolute bottom-4 left-4 text-white">
            <div class="text-xs font-bold uppercase tracking-widest opacity-90">
                {{ $event->starts_at->translatedFormat('D d M Y') }}
            </div>
            <div class="text-sm font-black">{{ $event->starts_at->format('H\hi') }}</div>
        </div>
    </a>

    <div class="p-6 flex-1 flex flex-col">
        <h3 class="text-lg font-black text-slate-900 dark:text-white leading-snug mb-2">
            <a href="{{ route('events.show', $event) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                {{ $event->title }}
            </a>
        </h3>

        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 mb-4">
            {{ \Illuminate\Support\Str::limit(strip_tags($event->description), 120) }}
        </p>

        <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400 mb-5">
            <li class="flex items-center gap-2">
                <i class="fa-solid fa-location-dot text-indigo-500 w-4"></i>
                <span class="truncate">{{ $event->venue }} &middot; {{ $event->city }}, {{ $event->country }}</span>
            </li>
            <li class="flex items-center gap-2">
                <i class="fa-solid fa-user-group text-indigo-500 w-4"></i>
                <span>{{ $event->capacity ? number_format($event->capacity, 0, ',', ' ').' places' : 'Places illimitées' }}</span>
            </li>
        </ul>

        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <span class="text-xs text-slate-400 dark:text-slate-500 truncate">
                par {{ $event->user?->name ?? 'Organisateur' }}
            </span>
            <a href="{{ route('events.show', $event) }}"
               class="inline-flex items-center gap-2 text-sm font-black text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                Voir
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</article>
