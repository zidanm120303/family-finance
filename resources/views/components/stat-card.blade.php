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

<x-card class="h-full p-4">
    <div class="flex min-w-0 items-center gap-3">
        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl {{ $toneClass }}">
            <img src="{{ asset('assets/svg/'.$icon) }}" class="h-6 w-6" alt="">
        </div>
        <div class="min-w-0">
            <div class="text-xs font-semibold text-slate-500">{{ $label }}</div>
            <div class="mt-1 break-words text-lg font-extrabold leading-tight tracking-tight text-slate-950 xl:text-xl">{{ $value }}</div>
            @if($hint)
                <div class="mt-1 truncate text-[11px] font-medium text-slate-500">{{ $hint }}</div>
            @endif
        </div>
    </div>
</x-card>
