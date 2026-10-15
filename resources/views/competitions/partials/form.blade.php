@php
    $candidate = $candidate ?? null;
    $inputClass = 'w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors';
    $labelClass = 'block text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2';
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @if(($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div>
        <label class="{{ $labelClass }}">Nom du candidat *</label>
        <input type="text" name="name" required value="{{ old('name', $candidate?->name) }}"
               placeholder="Ex : Team Étoile" class="{{ $inputClass }}">
    </div>

    <div>
        <label class="{{ $labelClass }}">Présentation</label>
        <textarea name="description" rows="3" placeholder="Quelques mots sur ce candidat..."
                  class="{{ $inputClass }} resize-y">{{ old('description', $candidate?->description) }}</textarea>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="{{ $labelClass }}">Téléphone</label>
            <input type="text" name="contact_phone" value="{{ old('contact_phone', $candidate?->contact_phone) }}"
                   placeholder="+228 ..." class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">Ordre d'affichage</label>
            <input type="number" name="sort_order" min="0" max="9999"
                   value="{{ old('sort_order', $candidate?->sort_order ?? 0) }}" class="{{ $inputClass }}">
        </div>
    </div>

    <div>
        <label class="{{ $labelClass }}">Photo (optionnel)</label>
        <input type="file" name="image" accept="image/*" class="{{ $inputClass }} file:mr-4 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-600 file:text-white">
    </div>

    <label class="flex items-center gap-3 text-sm font-bold text-slate-600 dark:text-slate-300 cursor-pointer">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $candidate?->is_active ?? true) ? 'checked' : '' }}
               class="w-5 h-5 rounded-lg border-2 border-slate-300 text-indigo-600 focus:ring-4 focus:ring-indigo-500/20">
        Visible par le public
    </label>

    <button type="submit" class="w-full px-6 py-3.5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
        <i class="fa-solid fa-check mr-2"></i>{{ $submitLabel }}
    </button>
</form>
