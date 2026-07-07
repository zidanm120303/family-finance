@extends('layouts.app')

@php
    $roleStyles = [
        'Kepala Keluarga' => ['bg-emerald-50 text-emerald-700', '♛'],
        'Ibu Rumah Tangga' => ['bg-rose-50 text-rose-700', '●'],
        'Anak' => ['bg-amber-50 text-amber-700', '◉'],
    ];
@endphp

@section('title', 'Anggota Keluarga - FamFinance')
@section('page_title', 'Anggota Keluarga')
@section('page_subtitle', 'Kelola anggota keluarga dan akses aplikasi FamFinance')

@section('content')
    <div class="page-stack" x-data="{ addOpen: false }">
        <section class="grid min-w-0 gap-4 xl:grid-cols-[390px_minmax(0,1fr)]">
            <x-card class="p-5">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-emerald-50"><img
                            src="{{ asset('assets/svg/icon-family.svg') }}" class="h-5 w-5" alt=""></span>
                    <h2 class="text-sm font-extrabold">Ringkasan Keluarga</h2>
                </div>
                <dl class="grid divide-y divide-slate-100 text-[11px]">
                    <div class="grid grid-cols-[120px_1fr] gap-3 py-3">
                        <dt class="font-medium text-slate-500">Kode Keluarga</dt>
                        <dd><span
                                class="rounded-lg bg-emerald-50 px-2 py-1 font-bold text-emerald-700">{{ $family?->family_code ?? '-' }}</span>
                        </dd>
                    </div>
                    <div class="grid grid-cols-[120px_1fr] gap-3 py-3">
                        <dt class="font-medium text-slate-500">Nama Keluarga</dt>
                        <dd class="font-bold text-slate-800">{{ $family?->family_name ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-[120px_1fr] gap-3 py-3">
                        <dt class="font-medium text-slate-500">Alamat</dt>
                        <dd class="font-medium leading-5 text-slate-700">{{ $family?->city ?? '-' }},
                            {{ $family?->province ?? '-' }} {{ $family?->postal_code }}</dd>
                    </div>
                    <div class="grid grid-cols-[120px_1fr] gap-3 py-3">
                        <dt class="font-medium text-slate-500">Jumlah Anggota</dt>
                        <dd class="font-bold text-slate-800">{{ $allMembers->count() }} orang</dd>
                    </div>
                </dl>
            </x-card>

            <x-card class="p-5">
                <div class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-violet-50"><img
                            src="{{ asset('assets/svg/icon-shield.svg') }}" class="h-5 w-5" alt=""></span>
                    <div>
                        <h2 class="text-sm font-extrabold">Ringkasan Role</h2>
                        <p class="mt-1 text-[10px] text-slate-500">Tiga role resmi dengan akses yang berbeda.</p>
                    </div>
                </div>
                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    @forelse($roles as $role)
                        @php([$style, $symbol] = $roleStyles[$role->role_name] ?? ['bg-slate-50 text-slate-700', '●'])
                        <article class="rounded-xl border border-slate-200 p-3">
                            <div class="flex items-start gap-3">
                                <span
                                    class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-lg {{ $style }}">{{ $symbol }}</span>
                                <div class="min-w-0">
                                    <div class="flex items-start justify-between gap-2"><b
                                            class="text-[11px] leading-4">{{ $role->role_name }}</b><span
                                            class="shrink-0 text-[9px] text-slate-400">{{ $role->family_users_count }}
                                            anggota</span></div>
                                    <p class="mt-2 line-clamp-2 text-[9px] font-medium leading-4 text-slate-500">
                                        {{ $role->description }}</p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="ff-empty md:col-span-3">Belum ada role.</div>
                    @endforelse
                </div>

            </x-card>
        </section>

        <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
            @if ($canManageMembers)
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="addOpen = true" class="primary-action">+ Tambah Anggota</button>
                    <button type="button" @click="addOpen = true" class="secondary-action text-blue-600">Undang via
                        Email</button>
                </div>
            @endif
            <form method="GET" action="{{ route('family.members') }}"
                class="grid min-w-0 gap-2 sm:grid-cols-[minmax(220px,1fr)_150px_auto]">
                <label class="relative min-w-0">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                        viewBox="0 0 24 24" fill="none">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
                        <path d="m20 20-4-4" stroke="currentColor" stroke-width="2" />
                    </svg>
                    <input name="search" value="{{ request('search') }}" class="form-control pl-10"
                        placeholder="Cari anggota...">
                </label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('status') === '1')>Aktif</option>
                    <option value="0" @selected(request('status') === '0')>Nonaktif</option>
                </select>
                <button class="secondary-action">Filter</button>
            </form>
        </div>

        <x-card class="overflow-hidden">
            <div class="ff-card-header">
                <div>
                    <h2 class="section-heading">Daftar Anggota</h2>
                    <p class="ff-muted mt-1">{{ $members->count() }} anggota ditampilkan</p>
                </div>
            </div>
            <div class="ff-table-wrap">
                <table class="ff-table min-w-[1000px]">
                    <thead>
                        <tr>
                            <th>Anggota</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            @if ($canManageMembers)
                                <th class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                            @php([$memberStyle] = $roleStyles[$member->role?->role_name] ?? ['bg-slate-50 text-slate-700', '●'])
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-slate-100 text-xs font-extrabold text-slate-700">{{ str($member->name)->substr(0, 2)->upper() }}</span>
                                        <div><b class="block whitespace-nowrap text-slate-950">{{ $member->name }}</b><span
                                                class="mt-1 block text-[9px] text-slate-500">{{ $member->role?->role_name ?? '-' }}
                                                @if ($member->id === auth()->id())
                                                    · Anda
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap">{{ $member->email }}</td>
                                <td class="whitespace-nowrap">{{ $member->username ?: '-' }}</td>
                                <td><span
                                        class="whitespace-nowrap rounded-lg px-2.5 py-1.5 text-[10px] font-bold {{ $memberStyle }}">{{ $member->role?->role_name ?? '-' }}</span>
                                </td>
                                <td class="whitespace-nowrap">{{ $member->phone ?: '-' }}</td>
                                <td><span
                                        class="rounded-lg px-2.5 py-1.5 text-[10px] font-bold {{ $member->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ $member->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </td>
                                <td class="whitespace-nowrap">
                                    {{ $member->last_login?->translatedFormat('d M Y') ?? '—' }}<span
                                        class="mt-1 block text-[9px] text-slate-400">{{ $member->last_login?->format('H:i') }}</span>
                                </td>
                                @if ($canManageMembers)
                                <td>
                                    <details class="group relative">
                                        <summary class="ff-icon-button mx-auto h-8 w-8 cursor-pointer list-none">⋮</summary>
                                        <div
                                            class="absolute right-0 z-20 mt-1 w-64 rounded-xl border border-slate-200 bg-white p-3 shadow-xl">
                                            <form method="POST" action="{{ route('family.members.update', $member) }}"
                                                class="grid gap-3">
                                                @csrf @method('PATCH')
                                                <label class="form-label">Role
                                                    <select name="role_id" class="form-control">
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}"
                                                                @selected($member->role_id === $role->id)>{{ $role->role_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </label>
                                                <label class="form-label">Status
                                                    <select name="is_active" class="form-control">
                                                        <option value="1" @selected($member->is_active)>Aktif</option>
                                                        <option value="0" @selected(!$member->is_active)>Nonaktif
                                                        </option>
                                                    </select>
                                                </label>
                                                <button class="primary-action w-full">Simpan Perubahan</button>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManageMembers ? 8 : 7 }}">
                                    <div class="ff-empty">Tidak ada anggota yang cocok.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        @if ($canManageMembers)
        <div x-show="addOpen" x-cloak>
            <button type="button" class="fixed inset-0 z-40 bg-slate-950/40" @click="addOpen = false"
                aria-label="Tutup panel"></button>
            <aside class="ff-drawer p-5" x-transition>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="section-heading">Tambah Anggota</h2>
                        <p class="ff-muted mt-1">Hubungkan akun baru ke {{ $family?->family_name }}.</p>
                    </div><button type="button" class="ff-icon-button" @click="addOpen = false">×</button>
                </div>
                <form method="POST" action="{{ route('family.members.store') }}" class="mt-6 grid gap-4">
                    @csrf
                    <label class="form-label">Nama Lengkap<input name="name" value="{{ old('name') }}"
                            class="form-control" required></label>
                    <label class="form-label">Email<input name="email" type="email" value="{{ old('email') }}"
                            class="form-control" required></label>
                    <label class="form-label">Username<input name="username" value="{{ old('username') }}"
                            class="form-control" placeholder="Opsional"></label>
                    <label class="form-label">Nomor HP<input name="phone" value="{{ old('phone') }}"
                            class="form-control" placeholder="Opsional"></label>
                    <label class="form-label">Role<select name="role_id" class="form-control" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected((int) old('role_id') === $role->id)>{{ $role->role_name }}
                                </option>
                            @endforeach
                        </select></label>
                    <label class="form-label">Password<input name="password" type="password" class="form-control"
                            required></label>
                    <button class="primary-action w-full">Simpan Anggota</button>
                </form>
            </aside>
        </div>
        @endif
    </div>
@endsection
