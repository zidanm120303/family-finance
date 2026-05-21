@extends('layouts.app')
@section('page_title', 'Kategori')
@section('page_subtitle', 'Dashboard > Kategori')
@section('content')
    @php
        $typeLabels = [
            'expense' => 'Pengeluaran',
            'income' => 'Pemasukan',
        ];
        $typeDescriptions = [
            'expense' => 'Kelola kategori pengeluaran untuk mencatat dan menganalisis keuangan keluarga dengan lebih baik.',
            'income' => 'Kelola kategori pemasukan agar sumber dana keluarga mudah dipantau setiap bulan.',
        ];
        $iconOptions = [
            ['label' => 'Dompet', 'value' => 'icon-wallet.svg', 'url' => asset('assets/svg/icon-wallet.svg')],
            ['label' => 'Pemasukan', 'value' => 'icon-income.svg', 'url' => asset('assets/svg/icon-income.svg')],
            ['label' => 'Pengeluaran', 'value' => 'icon-expense.svg', 'url' => asset('assets/svg/icon-expense.svg')],
            ['label' => 'Anggaran', 'value' => 'icon-budget.svg', 'url' => asset('assets/svg/icon-budget.svg')],
            ['label' => 'Listrik', 'value' => 'icon-lightning.svg', 'url' => asset('assets/svg/icon-lightning.svg')],
            ['label' => 'Internet', 'value' => 'icon-wifi.svg', 'url' => asset('assets/svg/icon-wifi.svg')],
            ['label' => 'Perisai', 'value' => 'icon-shield.svg', 'url' => asset('assets/svg/icon-shield.svg')],
            ['label' => 'Kesehatan', 'value' => 'icon-category-health.svg', 'url' => asset('assets/svg/icon-category-health.svg')],
        ];
        $colorOptions = ['#3B82F6', '#8B5CF6', '#F43F5E', '#F59E0B', '#22C55E', '#14B8A6', '#94A3B8'];
        $categoryPayload = $categories
            ->map(
                fn ($category) => [
                    'id' => $category->id,
                    'category_name' => $category->category_name,
                    'type' => $category->type,
                    'type_label' => $typeLabels[$category->type] ?? ucfirst($category->type),
                    'icon' => $category->icon ?: 'icon-wallet.svg',
                    'icon_url' => asset('assets/svg/' . ($category->icon ?: 'icon-wallet.svg')),
                    'color' => $category->color ?: '#10B981',
                    'description' => $category->description ?: '',
                    'is_default' => (bool) $category->is_default,
                    'transactions_count' => $category->transactions_count,
                    'update_url' => route('categories.update', $category),
                    'destroy_url' => route('categories.destroy', $category),
                ],
            )
            ->values();
        $totalCategories = $categories->count();
    @endphp

    <div class="categories-page"
        x-data="categoryPage({
            categories: @js($categoryPayload),
            iconOptions: @js($iconOptions),
            colorOptions: @js($colorOptions),
            typeDescriptions: @js($typeDescriptions),
            storeUrl: @js(route('categories.store')),
        })"
        x-init="init()">
        <section class="category-workspace">
            <div class="category-tabs">
                <button type="button" class="category-tab category-tab-expense" @click="setType('expense')"
                    :class="{ 'is-active': activeType === 'expense' }">
                    <span class="category-tab-icon">
                        <img src="{{ asset('assets/svg/icon-expense.svg') }}" alt="">
                    </span>
                    Pengeluaran
                </button>
                <button type="button" class="category-tab category-tab-income" @click="setType('income')"
                    :class="{ 'is-active': activeType === 'income' }">
                    <span class="category-tab-icon">
                        <img src="{{ asset('assets/svg/icon-income.svg') }}" alt="">
                    </span>
                    Pemasukan
                </button>
            </div>

            <div class="category-toolbar">
                <p x-text="typeDescriptions[activeType]"></p>
                <div class="category-toolbar-actions">
                    <form method="POST" action="{{ route('categories.import-default') }}">
                        @csrf
                        <button type="submit" class="category-soft-button">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 3v10m0 0 4-4m-4 4-4-4M5 17v2a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-2" />
                            </svg>
                            Import Default
                        </button>
                    </form>
                    <button type="button" class="category-primary-button" @click="startCreate()">
                        <span>+</span>
                        Tambah Kategori
                    </button>
                </div>
            </div>

            <x-card class="category-table-card">
                <div class="category-table-wrap">
                    <table class="category-table">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Tipe</th>
                                <th>Warna</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                @php
                                    $categoryColor = $category->color ?: ($category->type === 'income' ? '#10B981' : '#F43F5E');
                                    $categoryBg = preg_match('/^#[0-9A-Fa-f]{6}$/', $categoryColor)
                                        ? $categoryColor . '18'
                                        : '#F8FAFC';
                                    $categoryIcon = $category->icon ?: ($category->type === 'income' ? 'icon-income.svg' : 'icon-expense.svg');
                                    $typeTone = $category->type === 'income' ? 'income' : 'expense';
                                @endphp
                                <tr x-show="activeType === '{{ $category->type }}'" x-cloak class="category-table-row"
                                    @click="selectCategory({{ $category->id }})"
                                    :class="{ 'is-active': selected && selected.id === {{ $category->id }} }">
                                    <td data-label="Kategori">
                                        <div class="category-name-cell">
                                            <span class="category-list-icon"
                                                style="--category-color: {{ $categoryColor }}; --category-bg: {{ $categoryBg }};">
                                                <img src="{{ asset('assets/svg/' . $categoryIcon) }}" alt="">
                                            </span>
                                            <div class="min-w-0">
                                                <strong>{{ $category->category_name }}</strong>
                                                <span>{{ $category->transactions_count }} transaksi</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Tipe">
                                        <span class="category-type-pill category-type-{{ $typeTone }}">
                                            {{ $typeLabels[$category->type] ?? ucfirst($category->type) }}
                                        </span>
                                    </td>
                                    <td data-label="Warna">
                                        <span class="category-color-cell">
                                            <i style="background: {{ $categoryColor }}"></i>
                                            {{ $categoryColor }}
                                        </span>
                                    </td>
                                    <td data-label="Deskripsi" class="category-description-cell">
                                        {{ $category->description ?: 'Belum ada deskripsi' }}
                                    </td>
                                    <td data-label="Status">
                                        <span class="category-status category-status-{{ $category->is_default ? 'default' : 'custom' }}">
                                            {{ $category->is_default ? 'Default' : 'Custom' }}
                                        </span>
                                    </td>
                                    <td data-label="Aksi">
                                        <div class="category-row-actions">
                                            <button type="button" class="category-action-icon" title="Edit kategori"
                                                @click.stop="selectCategory({{ $category->id }})">
                                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="m4 20 4.4-1 10.3-10.3a2.1 2.1 0 0 0-3-3L5.4 16 4 20Z" />
                                                    <path d="m14.5 6.5 3 3" />
                                                </svg>
                                            </button>
                                            <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                                onsubmit="return confirm('Hapus kategori ini?')" @click.stop>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="category-action-icon category-action-danger"
                                                    title="Hapus kategori" @disabled($category->is_default)>
                                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                                        <path d="M4 7h16" />
                                                        <path d="M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="category-empty">Belum ada kategori.</td>
                                </tr>
                            @endforelse
                            @if ($categories->isNotEmpty())
                                <tr x-show="filteredCount() === 0" x-cloak>
                                    <td colspan="6" class="category-empty">
                                        Belum ada kategori <span x-text="activeTypeLabel().toLowerCase()"></span>.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="category-table-footer">
                    <span x-text="filteredSummary()"></span>
                    <div class="category-pagination">
                        <button type="button" disabled>&lsaquo;</button>
                        <button type="button" class="is-active">1</button>
                        <button type="button" disabled>&rsaquo;</button>
                    </div>
                </div>
            </x-card>
        </section>

        <aside class="category-detail-panel">
            <div class="category-detail-heading">
                <h2 x-text="mode === 'create' ? 'Tambah Kategori' : 'Detail Kategori'"></h2>
                <button type="button" class="category-panel-close" @click="startCreate()" title="Reset panel">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>

            <form id="category-editor-form" method="POST" :action="formAction()" class="category-editor-form">
                @csrf
                <input type="hidden" :name="mode === 'edit' ? '_method' : '_request_method'" value="PATCH">
                <input type="hidden" name="icon" x-model="form.icon">
                <input type="hidden" name="color" x-model="form.color">

                <div class="category-preview-block">
                    <span class="category-preview-icon" :style="`--category-color: ${form.color || '#10B981'}`">
                        <img :src="iconUrlFor(form.icon)" alt="">
                    </span>
                    <button type="button" class="category-soft-button category-change-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 3v3m0 12v3M5.6 5.6l2.1 2.1m8.6 8.6 2.1 2.1M3 12h3m12 0h3M5.6 18.4l2.1-2.1m8.6-8.6 2.1-2.1" />
                        </svg>
                        Ubah Ikon
                    </button>
                </div>

                <label class="category-field">
                    <span>Nama Kategori <b>*</b></span>
                    <input name="category_name" maxlength="100" required x-model="form.category_name">
                    <em x-text="`${(form.category_name || '').length}/100`"></em>
                </label>

                <label class="category-field">
                    <span>Tipe <b>*</b></span>
                    <select name="type" required x-model="form.type" @change="activeType = form.type">
                        <option value="expense">Pengeluaran</option>
                        <option value="income">Pemasukan</option>
                    </select>
                </label>

                <label class="category-field">
                    <span>Deskripsi</span>
                    <textarea name="description" rows="4" maxlength="150" x-model="form.description"></textarea>
                    <em x-text="`${(form.description || '').length}/150`"></em>
                </label>

                <div class="category-picker-group">
                    <span>Pilih Ikon</span>
                    <div class="category-icon-strip">
                        <template x-for="icon in iconOptions.slice(0, 6)" :key="`strip-${icon.value}`">
                            <button type="button" @click="form.icon = icon.value" :class="{ 'is-active': form.icon === icon.value }">
                                <img :src="icon.url" :alt="icon.label">
                            </button>
                        </template>
                    </div>
                    <div class="category-icon-grid">
                        <template x-for="icon in iconOptions" :key="icon.value">
                            <button type="button" @click="form.icon = icon.value" :class="{ 'is-active': form.icon === icon.value }">
                                <img :src="icon.url" :alt="icon.label">
                            </button>
                        </template>
                    </div>
                </div>

                <div class="category-picker-group">
                    <span>Pilih Warna</span>
                    <div class="category-color-picker">
                        <template x-for="color in colorOptions" :key="color">
                            <button type="button" :style="`background: ${color}`" @click="form.color = color"
                                :class="{ 'is-active': form.color === color }">
                                <i></i>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="category-picker-group">
                    <span>Status</span>
                    <label class="category-radio-card" :class="{ 'is-active': form.is_default }">
                        <input type="radio" disabled :checked="form.is_default">
                        <span>
                            <strong>Default</strong>
                            <small>Kategori bawaan sistem tidak dapat dihapus</small>
                        </span>
                    </label>
                    <label class="category-radio-card" :class="{ 'is-active': !form.is_default }">
                        <input type="radio" disabled :checked="!form.is_default">
                        <span>
                            <strong>Custom</strong>
                            <small>Kategori buatan sendiri dapat dihapus</small>
                        </span>
                    </label>
                </div>
            </form>

            <div class="category-panel-footer">
                <button type="button" class="category-soft-button category-cancel-button" @click="resetForm()">Batal</button>
                <button type="submit" form="category-editor-form" class="category-primary-button">
                    <span x-text="mode === 'create' ? 'Simpan Kategori' : 'Simpan Perubahan'"></span>
                </button>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
    <script>
        window.categoryPage = (config) => ({
            categories: config.categories || [],
            iconOptions: config.iconOptions || [],
            colorOptions: config.colorOptions || [],
            typeDescriptions: config.typeDescriptions || {},
            storeUrl: config.storeUrl,
            activeType: 'expense',
            mode: 'edit',
            selected: null,
            form: {},

            init() {
                const firstExpense = this.categories.find((category) => category.type === 'expense');
                const firstCategory = firstExpense || this.categories[0];

                if (firstCategory) {
                    this.selectCategory(firstCategory.id);
                    return;
                }

                this.startCreate();
            },

            setType(type) {
                this.activeType = type;
                const firstMatchingCategory = this.categories.find((category) => category.type === type);

                if (this.mode === 'create') {
                    this.form.type = type;
                    this.form.icon = this.defaultIcon();
                    this.form.color = this.defaultColor();
                    return;
                }

                if (firstMatchingCategory) {
                    this.selectCategory(firstMatchingCategory.id);
                    return;
                }

                this.startCreate();
            },

            selectCategory(id) {
                const category = this.categories.find((item) => item.id === id);

                if (!category) {
                    this.startCreate();
                    return;
                }

                this.mode = 'edit';
                this.selected = category;
                this.activeType = category.type;
                this.form = {
                    category_name: category.category_name,
                    type: category.type,
                    icon: category.icon || this.defaultIcon(),
                    color: category.color || this.defaultColor(),
                    description: category.description || '',
                    is_default: Boolean(category.is_default),
                };
            },

            startCreate() {
                this.mode = 'create';
                this.selected = null;
                this.form = {
                    category_name: '',
                    type: this.activeType,
                    icon: this.defaultIcon(),
                    color: this.defaultColor(),
                    description: '',
                    is_default: false,
                };
            },

            resetForm() {
                if (this.selected) {
                    this.selectCategory(this.selected.id);
                    return;
                }

                this.startCreate();
            },

            formAction() {
                if (this.mode === 'edit' && this.selected) {
                    return this.selected.update_url;
                }

                return this.storeUrl;
            },

            defaultIcon() {
                return this.activeType === 'income' ? 'icon-income.svg' : 'icon-expense.svg';
            },

            defaultColor() {
                return this.activeType === 'income' ? '#10B981' : '#F43F5E';
            },

            iconUrlFor(iconValue) {
                const selectedIcon = this.iconOptions.find((icon) => icon.value === iconValue);
                const fallbackIcon = this.iconOptions.find((icon) => icon.value === this.defaultIcon()) || this.iconOptions[0];

                return (selectedIcon || fallbackIcon || {}).url || '';
            },

            filteredCount() {
                return this.categories.filter((category) => category.type === this.activeType).length;
            },

            activeTypeLabel() {
                return this.activeType === 'income' ? 'Pemasukan' : 'Pengeluaran';
            },

            filteredSummary() {
                const count = this.filteredCount();

                if (count === 0) {
                    return `Menampilkan 0 kategori ${this.activeTypeLabel().toLowerCase()}`;
                }

                return `Menampilkan 1 - ${count} dari ${count} kategori ${this.activeTypeLabel().toLowerCase()}`;
            },
        });
    </script>
@endpush
