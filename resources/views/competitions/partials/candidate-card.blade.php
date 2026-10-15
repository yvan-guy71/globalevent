@php
    $rank = $rank ?? null;
    $userVote = $userVotes[$candidate->id] ?? null;
    $image = $candidate->image_url;
    $medals = [1 => 'bg-amber-400 text-amber-950', 2 => 'bg-slate-300 text-slate-800', 3 => 'bg-orange-400 text-orange-950'];
    $medalClass = $medals[$rank] ?? 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400';
@endphp

<article class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm flex flex-col">
    <div class="flex items-start gap-4">
        <div class="relative shrink-0">
            @if($image)
                <img src="{{ $image }}" alt="{{ $candidate->name }}" class="w-16 h-16 rounded-2xl object-cover">
            @else
                <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-2xl font-black">
                    {{ mb_strtoupper(mb_substr($candidate->name, 0, 1)) }}
                </div>
            @endif
            @if($rank)
                <span class="absolute -top-2 -left-2 w-7 h-7 rounded-full {{ $medalClass }} flex items-center justify-center text-xs font-black shadow">
                    {{ $rank }}
                </span>
            @endif
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex items-start justify-between gap-3">
                <h3 class="font-black text-lg leading-tight text-slate-900 dark:text-white">
                    {{ $candidate->name }}
                </h3>
                <span class="shrink-0 inline-flex flex-col items-center px-3 py-1.5 rounded-2xl {{ $candidate->score >= 0 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300' }}">
                    <span class="text-lg font-black leading-none">{{ $candidate->score }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider">score</span>
                </span>
            </div>

            @if($candidate->description)
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 line-clamp-3">{{ $candidate->description }}</p>
            @endif

            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3 text-xs font-bold text-slate-400 dark:text-slate-500">
                <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                    <i class="fa-solid fa-thumbs-up"></i>{{ $candidate->votes_up }} pour
                </span>
                <span class="flex items-center gap-1.5 text-rose-600 dark:text-rose-400">
                    <i class="fa-solid fa-thumbs-down"></i>{{ $candidate->votes_down }} contre
                </span>
                @if(! $candidate->is_active)
                    <span class="text-amber-600 dark:text-amber-400"><i class="fa-solid fa-eye-slash"></i> Masqué</span>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center gap-2">
        @if($canVote)
            <form action="{{ route('competitions.vote', [$event, $candidate]) }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="value" value="1">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl font-black text-sm transition-colors {{ $userVote && $userVote->isFor() ? 'bg-emerald-600 text-white' : 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50' }}">
                    <i class="fa-solid fa-thumbs-up"></i>
                    {{ $userVote && $userVote->isFor() ? 'Voté pour' : 'Voter pour' }}
                </button>
            </form>
            <form action="{{ route('competitions.vote', [$event, $candidate]) }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="value" value="-1">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl font-black text-sm transition-colors {{ $userVote && $userVote->isAgainst() ? 'bg-rose-600 text-white' : 'bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-900/50' }}">
                    <i class="fa-solid fa-thumbs-down"></i>
                    {{ $userVote && $userVote->isAgainst() ? 'Voté contre' : 'Voter contre' }}
                </button>
            </form>
        @elseif($canManage)
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 flex-1">
                <i class="fa-solid fa-circle-info mr-1"></i> En tant qu'organisateur, vous ne participez pas au vote.
            </p>
        @else
            <a href="{{ route('login') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-black text-sm hover:bg-indigo-100 dark:hover:bg-indigo-900/50">
                <i class="fa-solid fa-right-to-bracket"></i> Connectez-vous pour voter
            </a>
        @endif
    </div>

    @if($canManage)
        <details class="mt-4 group">
            <summary class="cursor-pointer text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 list-none">
                <i class="fa-solid fa-pen mr-1"></i> Modifier le candidat
            </summary>
            <div class="mt-4">
                @include('competitions.partials.form', [
                    'action' => route('competitions.candidates.update', [$event, $candidate]),
                    'method' => 'PUT',
                    'candidate' => $candidate,
                    'submitLabel' => 'Enregistrer',
                ])
                <form action="{{ route('competitions.candidates.destroy', [$event, $candidate]) }}" method="POST" class="mt-4"
                      onsubmit="return confirm('Retirer « {{ $candidate->name }} » de la compétition ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 font-black text-sm hover:bg-rose-100 dark:hover:bg-rose-900/50 transition-colors">
                        <i class="fa-solid fa-trash"></i> Retirer ce candidat
                    </button>
                </form>
            </div>
        </details>
    @endif
</article>
