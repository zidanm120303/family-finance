@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $classes = [
        'primary' => 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-[0_12px_24px_rgba(16,185,129,0.22)] hover:from-emerald-600 hover:to-emerald-700',
        'secondary' => 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700',
        'blue' => 'bg-blue-600 text-white hover:bg-blue-700',
        'purple' => 'bg-violet-600 text-white hover:bg-violet-700',
    ][$variant] ?? 'bg-slate-900 text-white';
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3 text-sm font-extrabold transition {$classes}"]) }}>
    {{ $slot }}
</button>
