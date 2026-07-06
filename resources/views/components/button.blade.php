@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $classes = [
        'primary' => 'bg-emerald-600 text-white shadow-sm hover:bg-emerald-700',
        'secondary' => 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700',
        'blue' => 'bg-blue-600 text-white hover:bg-blue-700',
        'purple' => 'bg-violet-600 text-white hover:bg-violet-700',
    ][$variant] ?? 'bg-slate-900 text-white';
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex h-11 items-center justify-center gap-2 whitespace-nowrap rounded-xl px-4 text-[13px] font-bold transition focus:outline-none focus:ring-4 focus:ring-emerald-100 {$classes}"]) }}>
    {{ $slot }}
</button>
