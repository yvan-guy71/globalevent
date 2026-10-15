@extends('layouts.app')

@section('title', 'Modifier '.$event->title.' - Event Global')

@section('content')
    <section class="bg-slate-900 pt-16 pb-24 px-4">
        <div class="max-w-5xl mx-auto">
            <nav class="text-xs font-bold uppercase tracking-widest text-indigo-300/80 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Accueil</a>
                <span class="mx-2">/</span>
                <a href="{{ route('events.mine') }}" class="hover:text-white">Mes évènements</a>
                <span class="mx-2">/</span>
                <span class="text-white">Modification</span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Modifier l'évènement</h1>
            <p class="text-indigo-100">{{ $event->title }}</p>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-4 -mt-12 relative z-10 pb-20">
        @if($errors->any())
            <div class="mb-8 p-5 rounded-2xl bg-rose-50 dark:bg-rose-900/30 border-l-4 border-rose-500 text-rose-700 dark:text-rose-300">
                <p class="font-black mb-2 flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation"></i> Merci de corriger les erreurs suivantes :</p>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('events.partials.form', ['event' => $event])

            <div class="mt-8 flex flex-col sm:flex-row items-center gap-4">
                <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-100 dark:shadow-none">
                    <i class="fa-solid fa-floppy-disk mr-2"></i>Enregistrer les modifications
                </button>
                <a href="{{ route('events.show', $event) }}" class="w-full sm:w-auto text-center px-8 py-4 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Retour à l'évènement
                </a>
            </div>
        </form>

        <form action="{{ route('events.destroy', $event) }}" method="POST" class="mt-4"
              onsubmit="return confirm('Supprimer définitivement cet évènement ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 font-black rounded-2xl hover:bg-rose-100 dark:hover:bg-rose-900/50 transition-colors">
                <i class="fa-solid fa-trash mr-2"></i>Supprimer cet évènement
            </button>
        </form>
    </section>
@endsection
