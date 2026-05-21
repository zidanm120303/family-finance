@props([
    'label',
    'value' => 0,
    'meta' => null,
    'tone' => 'emerald',
])

@php
    $barClass = [
        'emerald' => 'bg-emerald-500',
        'rose' => 'bg-rose-500',
        'amber' => 'bg-amber-500',
        'blue' => 'bg-blue-500',
        'purple' => 'bg-violet-500',
    ][$tone] ?? 'bg-emerald-500';
    $width = min(100, max(0, (float) $value));
@endphp

<div {{ $attributes }}>
    <div class="mb-2 flex items-center justify-between gap-3 text-sm">
        <span class="truncate font-bold text-slate-800">{{ $label }}</span>
        <span class="shrink-0 font-extrabold text-slate-950">{{ round((float) $value) }}%</span>
    </div>
    <div class="h-2.5 rounded-full bg-slate-100">
        <div class="h-2.5 rounded-full {{ $barClass }}" style="width: {{ $width }}%"></div>
    </div>
    @if($meta)
        <div class="mt-2 text-xs font-semibold text-slate-500">{{ $meta }}</div>
    @endif
</div>
