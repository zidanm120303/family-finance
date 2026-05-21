@extends('layouts.app')
@section('page_title', 'Anggota Keluarga')
@section('page_subtitle', 'Kelola anggota keluarga dan akses aplikasi FamFinance.')
@section('content')
    @php
        $roleMeta = [
            'Kepala Keluarga' => [
                'tone' => 'green',
                'icon' => 'KK',
                'description' => 'Akses penuh ke semua fitur dan pengaturan.',
            ],
            'Ibu' => ['tone' => 'rose', 'icon' => 'IB', 'description' => 'Kelola transaksi, anggaran, dan laporan.'],
            'Anak' => [
                'tone' => 'amber',
                'icon' => 'AK',
                'description' => 'Akses terbatas, dapat melihat dan input tertentu.',
            ],
            'Admin Keluarga' => [
                'tone' => 'purple',
                'icon' => 'AD',
                'description' => 'Kelola anggota, role, dan pengaturan keluarga.',
            ],
        ];
        $roleTone = fn($roleName) => $roleMeta[$roleName]['tone'] ?? 'slate';
        $currentUserId = auth()->id();
    @endphp

    <div class="family-page" x-data="{ showForm: false }">
        <section class="family-summary-grid">
            <x-card class="family-info-card">
                <div class="family-card-title">
                    <span class="family-title-icon family-title-green">
                        <img src="{{ asset('assets/svg/icon-family.svg') }}" alt="">
                    </span>
                    <h2>Ringkasan Keluarga</h2>
                </div>
                <dl class="family-info-list">
                    <div>
                        <dt>Kode Keluarga</dt>
                        <dd>
                            <span class="family-code">{{ $family?->family_code ?? '-' }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt>Nama Keluarga</dt>
                        <dd>{{ $family?->family_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt>Alamat</dt>
                        <dd>{{ collect([$family?->address, $family?->city, $family?->province, $family?->postal_code])->filter()->join(', ') ?:'-' }}
                        </dd>
                    </div>
                    <div>
                        <dt>Jumlah Anggota</dt>
                        <dd>{{ $allMembers->count() }} orang</dd>
                    </div>
                </dl>
            </x-card>

            <x-card class="family-role-card">
                <div class="family-card-title">
                    <span class="family-title-icon family-title-purple">
                        <img src="{{ asset('assets/svg/icon-shield.svg') }}" alt="">
                    </span>
                    <div>
                        <h2>Ringkasan Role</h2>
                        <p>Setiap role memiliki akses dan izin yang berbeda.</p>
                    </div>
                </div>
                <div class="family-role-grid">
                    @foreach ($roles as $role)
                        @php
                            $meta = $roleMeta[$role->role_name] ?? [
                                'tone' => 'slate',
                                'icon' => 'RL',
                                'description' => $role->description ?: 'Role keluarga.',
                            ];
                        @endphp
                        <article class="family-role-item family-role-{{ $meta['tone'] }}">
                            <span>{{ $meta['icon'] }}</span>
                            <div>
                                <strong>{{ $role->role_name }}</strong>
                                <p>{{ $meta['description'] }}</p>
                            </div>
                            <em>{{ $role->family_users_count }} anggota</em>
                        </article>
                    @endforeach
                </div>
                <a href="#family-members-table" class="family-role-link">Lihat detail izin setiap role &rarr;</a>
            </x-card>
        </section>

        <section class="family-actions-row">
            <div class="family-action-buttons">
                <button type="button" class="family-primary-button" @click="showForm = !showForm">
                    <span>+</span>
                    Tambah Anggota
                </button>
                {{-- <button type="button" class="family-blue-button">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 6h16v12H4z" />
                        <path d="m4 7 8 6 8-6" />
                    </svg>
                    Undang via Email
                </button>
                <button type="button" class="family-purple-button">
                    <img src="{{ asset('assets/svg/icon-shield.svg') }}" alt="">
                    Kelola Role
                </button> --}}
            </div>

            <form method="GET" action="{{ route('family.members') }}" class="family-filter-form">
                <label>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m21 21-4.5-4.5M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                    </svg>
                    <input name="search" value="{{ request('search') }}" placeholder="Cari anggota...">
                </label>
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('status') === '1')>Aktif</option>
                    <option value="0" @selected(request('status') === '0')>Nonaktif</option>
                </select>
                <button type="submit" title="Filter">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 5h16l-6.5 7.5V19l-3 1v-7.5L4 5Z" />
                    </svg>
                </button>
            </form>
        </section>

        <x-card class="family-form-card" x-show="showForm" x-cloak>
            <div class="family-card-title">
                <span class="family-title-icon family-title-green">
                    <img src="{{ asset('assets/svg/icon-family.svg') }}" alt="">
                </span>
                <h2>Tambah Anggota Baru</h2>
            </div>
            <form method="POST" action="{{ route('family.members.store') }}" class="family-member-form">
                @csrf
                <label><span>Nama</span><input name="name" required></label>
                <label><span>Email</span><input name="email" type="email" required></label>
                <label><span>Username</span><input name="username"></label>
                <label><span>Nomor HP</span><input name="phone"></label>
                <label>
                    <span>Role</span>
                    <select name="role_id" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label><span>Password</span><input name="password" type="password" required></label>
                <button type="submit" class="family-primary-button">Simpan Anggota</button>
            </form>
        </x-card>

        <x-card class="family-table-card" id="family-members-table">
            <div class="family-table-wrap">
                <table class="family-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $member)
                            @php
                                $tone = $roleTone($member->role?->role_name);
                            @endphp
                            <tr>
                                <td data-label="Foto">
                                    <span class="family-avatar">{{ str($member->name)->substr(0, 2)->upper() }}</span>
                                </td>
                                <td data-label="Nama" class="family-name-cell">
                                    <strong>{{ $member->name }}</strong>
                                    @if ($member->id === $currentUserId)
                                        <span>Anda</span>
                                    @endif
                                    <small>{{ $member->role?->role_name ?? '-' }}</small>
                                </td>
                                <td data-label="Email">{{ $member->email }}</td>
                                <td data-label="Username">{{ $member->username ?: '-' }}</td>
                                <td data-label="Role">
                                    <span class="family-role-badge family-badge-{{ $tone }}">
                                        {{ $member->role?->role_name ?? '-' }}
                                    </span>
                                </td>
                                <td data-label="Phone">{{ $member->phone ?: '-' }}</td>
                                <td data-label="Status">
                                    <span class="family-status {{ $member->is_active ? 'is-active' : 'is-inactive' }}">
                                        {{ $member->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td data-label="Last Login">
                                    {{ $member->last_login?->translatedFormat('d M Y') ?? '-' }}
                                    @if ($member->last_login)
                                        <small>{{ $member->last_login->format('H:i') }}</small>
                                    @endif
                                </td>
                                <td data-label="Aksi">
                                    <form method="POST" action="{{ route('family.members.update', $member) }}"
                                        class="family-row-form">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role_id" title="Role">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" @selected($member->role_id === $role->id)>
                                                    {{ $role->role_name }}</option>
                                            @endforeach
                                        </select>
                                        <select name="is_active" title="Status">
                                            <option value="1" @selected($member->is_active)>Aktif</option>
                                            <option value="0" @selected(!$member->is_active)>Nonaktif</option>
                                        </select>
                                        <button type="submit" class="family-icon-button" title="Simpan">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="m4 20 4.4-1 10.3-10.3a2.1 2.1 0 0 0-3-3L5.4 16 4 20Z" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="family-empty">Tidak ada anggota yang cocok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="family-table-footer">
                <span>Menampilkan 1 - {{ $members->count() }} dari {{ $allMembers->count() }} anggota</span>
                <div class="family-pagination">
                    <button type="button" disabled>&lsaquo;</button>
                    <button type="button" class="is-active">1</button>
                    <button type="button" disabled>&rsaquo;</button>
                </div>
            </div>
        </x-card>
    </div>
@endsection
