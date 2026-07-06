@props(['tone' => 'slate'])

@php
    $classes = [
        'income' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        'expense' => 'bg-rose-50 text-rose-700 ring-rose-100',
        'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        'cancel' => 'bg-rose-50 text-rose-700 ring-rose-100',
        'blue' => 'bg-blue-50 text-blue-700 ring-blue-100',
        'purple' => 'bg-violet-50 text-violet-700 ring-violet-100',
        'slate' => 'bg-slate-50 text-slate-700 ring-slate-100',
    ][$tone] ?? 'bg-slate-50 text-slate-700 ring-slate-100';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold ring-1 {$classes}"]) }}>
    {{ $slot }}
</span>
