@php
    $tabs = [
        ['route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'fa-calendar-star', 'label' => 'Évènements'],
        ['route' => 'admin.users', 'active' => 'admin.users*', 'icon' => 'fa-users', 'label' => 'Utilisateurs'],
    ];
@endphp

<nav class="flex flex-wrap gap-2 mb-8">
    @foreach($tabs as $tab)
        @php $isActive = request()->routeIs($tab['active']); @endphp
        <a href="{{ route($tab['route']) }}"
           class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-sm font-black transition-colors {{ $isActive ? 'bg-indigo-600 text-white shadow-strong' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
            <i class="fa-solid {{ $tab['icon'] }}"></i> {{ $tab['label'] }}
        </a>
    @endforeach
</nav>
