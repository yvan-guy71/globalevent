@extends('layouts.app')

@section('title', 'Ouvrir une cagnotte - Event Global')

@section('content')
    <section class="bg-slate-900 py-16 px-4">
        <div class="max-w-3xl mx-auto">
            <nav class="text-xs font-bold uppercase tracking-widest text-indigo-300/80 mb-4">
                <a href="{{ route('fundraisers.index') }}" class="hover:text-white">Cagnottes</a>
                <span class="mx-2">/</span>
                <span class="text-white">Nouvelle cagnotte</span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Ouvrir une cagnotte</h1>
            <p class="text-indigo-100">Décrivez votre projet, fixez un objectif et partagez votre collecte.</p>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-4 py-14">
        @if($errors->any())
            <div class="mb-8 p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/30 border-l-4 border-rose-500 text-rose-700 dark:text-rose-300 text-sm font-bold">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
            @include('fundraisers.partials.form', [
                'action' => route('fundraisers.store'),
                'method' => 'POST',
                'fundraiser' => null,
                'submitLabel' => 'Créer ma cagnotte',
            ])
        </div>
    </section>
@endsection
