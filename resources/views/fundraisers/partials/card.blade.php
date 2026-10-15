@php
    $image = $fundraiser->image_url;
    $percent = $fundraiser->progress_percent;
@endphp

<article class="group bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-strong transition-all duration-300 flex flex-col h-full">
    <a href="{{ route('fundraisers.show', $fundraiser) }}" class="relative block h-44 overflow-hidden">
        @if($image)
            <img src="{{ $image }}" alt="{{ $fundraiser->title }}" loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
        @else
            <div class="w-full h-full bg-gradient-to-br from-indigo-600 to-violet-700 flex items-center justify-center">
                <i class="fa-solid fa-hand-holding-heart text-white/80 text-5xl"></i>
            </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

        <span class="absolute top-4 left-4 bg-rose-600 text-white text-[11px] font-black uppercase tracking-wider px-3 py-1.5 rounded-full shadow-lg">
            Cagnotte
        </span>

        @if($fundraiser->is_reached)
            <span class="absolute top-4 right-4 bg-emerald-500 text-white text-[11px] font-black uppercase tracking-wider px-3 py-1.5 rounded-full shadow-lg">
                <i class="fa-solid fa-circle-check mr-1"></i> Objectif atteint
            </span>
        @endif
    </a>

    <div class="p-6 flex-1 flex flex-col">
        <h3 class="text-lg font-black text-slate-900 dark:text-white leading-snug mb-2">
            <a href="{{ route('fundraisers.show', $fundraiser) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                {{ $fundraiser->title }}
            </a>
        </h3>

        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 mb-5">
            {{ \Illuminate\Support\Str::limit(strip_tags($fundraiser->description), 110) }}
        </p>

        <div class="mt-auto">
            <div class="flex items-end justify-between mb-2">
                <span class="text-lg font-black text-indigo-600 dark:text-indigo-400">{{ $fundraiser->raised_label }}</span>
                <span class="text-xs font-bold text-slate-400 dark:text-slate-500">sur {{ $fundraiser->goal_label }}</span>
            </div>
            <div class="w-full h-2.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                <div class="h-full rounded-full {{ $fundraiser->is_reached ? 'bg-emerald-500' : 'bg-indigo-600' }}" style="width: {{ $percent }}%"></div>
            </div>
            <div class="flex items-center justify-between mt-3 text-xs font-bold text-slate-400 dark:text-slate-500">
                <span>{{ $percent }}% financés</span>
                <span>{{ $fundraiser->donors_count }} don{{ $fundraiser->donors_count > 1 ? 's' : '' }}</span>
            </div>

            <a href="{{ route('fundraisers.show', $fundraiser) }}"
               class="mt-5 w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
                <i class="fa-solid fa-hand-holding-dollar"></i> Faire un don
            </a>
        </div>
    </div>
</article>
