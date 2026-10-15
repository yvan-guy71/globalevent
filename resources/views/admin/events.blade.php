@extends('layouts.app')

@section('title', 'Administration - Event Global')

@section('content')
    <section class="bg-slate-900 pt-16 pb-24 px-4">
        <div class="max-w-7xl mx-auto">
            <nav class="text-xs font-bold uppercase tracking-widest text-indigo-300/80 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Accueil</a>
                <span class="mx-2">/</span>
                <span class="text-white">Administration</span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Panel administrateur</h1>
            <p class="text-indigo-100">Supervisez les évènements et les comptes de la plateforme.</p>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 -mt-12 relative z-10 pb-20">
        @include('admin.partials.nav')

        @php
            $statBlocks = [
                ['icon' => 'fa-calendar-star', 'value' => $stats['events'], 'label' => 'Évènements'],
                ['icon' => 'fa-circle-check', 'value' => $stats['published'], 'label' => 'Publiés'],
                ['icon' => 'fa-pen-ruler', 'value' => $stats['drafts'], 'label' => 'Brouillons'],
                ['icon' => 'fa-users', 'value' => $stats['users'], 'label' => 'Utilisateurs'],
            ];
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @foreach($statBlocks as $block)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6">
                    <i class="fa-solid {{ $block['icon'] }} text-indigo-600 dark:text-indigo-400 mb-2"></i>
                    <div class="text-3xl font-black">{{ number_format($block['value'], 0, ',', ' ') }}</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ $block['label'] }}</div>
                </div>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.dashboard') }}"
              class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 flex flex-col md:flex-row gap-4 mb-8">
            <div class="flex-1 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Rechercher un évènement..."
                       class="w-full pl-11 pr-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-medium focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <select name="status" class="px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">Tous les statuts</option>
                @foreach(config('events.statuses') as $slug => $label)
                    <option value="{{ $slug }}" {{ $filters['status'] === $slug ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700">Filtrer</button>
        </form>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <th class="px-6 py-4">Évènement</th>
                            <th class="px-6 py-4 hidden md:table-cell">Organisateur</th>
                            <th class="px-6 py-4 hidden lg:table-cell">Date</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($events as $event)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <img src="{{ $event->image_url ?: asset($event->fallback_image) }}" alt=""
                                             class="w-12 h-12 rounded-xl object-cover shrink-0">
                                        <div class="min-w-0">
                                            <a href="{{ route('events.show', $event) }}" class="font-bold hover:text-indigo-600 dark:hover:text-indigo-400 line-clamp-1">
                                                {{ $event->title }}
                                            </a>
                                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                                                {{ $event->type_label }} @if($event->is_featured) &middot; À la une @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-300">
                                    {{ $event->user?->name }}<br>
                                    <span class="text-xs text-slate-400">{{ $event->user?->email }}</span>
                                </td>
                                <td class="px-6 py-4 hidden lg:table-cell text-sm text-slate-600 dark:text-slate-300">
                                    {{ $event->starts_at->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusStyles = [
                                            'published' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                            'draft' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                            'cancelled' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-black {{ $statusStyles[$event->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $event->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('events.edit', $event) }}" title="Modifier"
                                           class="w-9 h-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </a>
                                        <form action="{{ route('events.destroy', $event) }}" method="POST"
                                              onsubmit="return confirm('Supprimer définitivement « {{ $event->title }} » ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Supprimer"
                                                    class="w-9 h-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30">
                                                <i class="fa-solid fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                    Aucun évènement trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-10">
            {{ $events->links() }}
        </div>
    </section>
@endsection
