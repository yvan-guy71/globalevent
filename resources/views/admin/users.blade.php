@extends('layouts.app')

@section('title', 'Utilisateurs - Administration - Event Global')

@section('content')
    <section class="bg-slate-900 pt-16 pb-24 px-4">
        <div class="max-w-7xl mx-auto">
            <nav class="text-xs font-bold uppercase tracking-widest text-indigo-300/80 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Accueil</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Administration</a>
                <span class="mx-2">/</span>
                <span class="text-white">Utilisateurs</span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Gestion des utilisateurs</h1>
            <p class="text-indigo-100">Consultez les comptes, attribuez des rôles et supprimez des comptes.</p>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 -mt-12 relative z-10 pb-20">
        @include('admin.partials.nav')

        @php
            $statBlocks = [
                ['icon' => 'fa-users', 'value' => $stats['total'], 'label' => 'Utilisateurs'],
                ['icon' => 'fa-user-shield', 'value' => $stats['admins'], 'label' => 'Administrateurs'],
                ['icon' => 'fa-bullhorn', 'value' => $stats['organizers'], 'label' => 'Organisateurs'],
                ['icon' => 'fa-user', 'value' => $stats['clients'], 'label' => 'Clients'],
            ];
            $roleStyles = [
                'admin' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
                'organisateur' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                'client' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
            ];
            $roleLabels = [
                'admin' => 'Administrateur',
                'organisateur' => 'Organisateur',
                'client' => 'Client',
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

        <form method="GET" action="{{ route('admin.users') }}"
              class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 flex flex-col md:flex-row gap-4 mb-8">
            <div class="flex-1 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Rechercher un nom, un email, un téléphone..."
                       class="w-full pl-11 pr-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-medium focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <select name="role" class="px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">Tous les rôles</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}" {{ $filters['role'] === $role ? 'selected' : '' }}>{{ $roleLabels[$role] }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700">Filtrer</button>
        </form>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <th class="px-6 py-4">Utilisateur</th>
                            <th class="px-6 py-4 hidden md:table-cell">Contact</th>
                            <th class="px-6 py-4 hidden lg:table-cell">Activité</th>
                            <th class="px-6 py-4">Rôle</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <span class="w-11 h-11 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black shrink-0">
                                            {{ $user->initials() }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 dark:text-white line-clamp-1">
                                                {{ $user->name }}
                                                @if($user->is(auth()->user()))
                                                    <span class="text-xs font-bold text-indigo-500">(vous)</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-300">
                                    {{ $user->phone ?: '—' }}<br>
                                    <span class="text-xs text-slate-400">{{ $user->organization ?: 'Particulier' }}</span>
                                </td>
                                <td class="px-6 py-4 hidden lg:table-cell text-sm text-slate-600 dark:text-slate-300">
                                    <span class="font-bold">{{ $user->events_count }}</span> évènement{{ $user->events_count > 1 ? 's' : '' }}
                                    &middot;
                                    <span class="font-bold">{{ $user->orders_count }}</span> commande{{ $user->orders_count > 1 ? 's' : '' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-black {{ $roleStyles[$user->role] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($user->is(auth()->user()))
                                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 px-3">Compte connecté</span>
                                        @else
                                            <form action="{{ route('admin.users.role', $user) }}" method="POST" class="flex items-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                <select name="role" onchange="this.form.submit()"
                                                        class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold focus:ring-2 focus:ring-indigo-500 outline-none">
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>{{ $roleLabels[$role] }}</option>
                                                    @endforeach
                                                </select>
                                            </form>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                  onsubmit="return confirm('Supprimer définitivement le compte de « {{ $user->name }} » ainsi que ses évènements et commandes ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Supprimer"
                                                        class="w-9 h-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30">
                                                    <i class="fa-solid fa-trash text-sm"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                    Aucun utilisateur trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-10">
            {{ $users->links() }}
        </div>
    </section>
@endsection
