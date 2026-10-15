@php
    $size = $size ?? 'md';
    $height = $size === 'sm' ? '2.25rem' : '2.75rem';
@endphp

<a href="{{ route('home') }}" class="inline-flex items-center group">
    <img src="{{ asset('images/logo.png') }}"
         alt="Event Global"
         style="height: {{ $height }}; width: auto;"
         class="transition-transform duration-200 group-hover:scale-105">
</a>
