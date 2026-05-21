@props([
    'label',
    'value',
    'icon' => 'icon-wallet.svg',
    'tone' => 'emerald',
    'hint' => null,
])

@php
    $toneClass = [
        'emerald' => 'bg-emerald-50 text-emerald-700',
        'rose' => 'bg-rose-50 text-rose-700',
        'amber' => 'bg-amber-50 text-amber-700',
        'blue' => 'bg-blue-50 text-blue-700',
        'purple' => 'bg-violet-50 text-violet-700',
    ][$tone] ?? 'bg-slate-50 text-slate-700';
@endphp

<x-card class="p-5">
    <div class="flex items-center gap-4">
        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl {{ $toneClass }}">
            <img src="{{ asset('assets/svg/'.$icon) }}" class="h-8 w-8" alt="">
        </div>
        <div class="min-w-0">
            <div class="text-sm font-semibold text-slate-500">{{ $label }}</div>
            <div class="mt-1 break-words text-xl font-extrabold leading-tight tracking-tight text-slate-950 sm:text-2xl">{{ $value }}</div>
            @if($hint)
                <div class="mt-1 text-xs font-semibold text-slate-500">{{ $hint }}</div>
            @endif
        </div>
    </div>
</x-card>
