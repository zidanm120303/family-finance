<form method="POST" action="{{ route('categories.store') }}" class="mt-5 grid gap-4">
    @csrf
    <label class="{{ $labelClass }}">Nama Kategori
        <input name="category_name" value="{{ old('category_name') }}" class="{{ $fieldClass }}" placeholder="Contoh: Belanja Bulanan" required>
    </label>
    <label class="{{ $labelClass }}">Tipe
        <select name="type" class="{{ $fieldClass }}" required>
            <option value="expense" @selected(old('type', 'expense') === 'expense')>Pengeluaran</option>
            <option value="income" @selected(old('type') === 'income')>Pemasukan</option>
        </select>
    </label>
    <label class="{{ $labelClass }}">Ikon
        <select name="icon" class="{{ $fieldClass }}">
            @foreach($icons as $icon => $label)
                <option value="{{ $icon }}" @selected(old('icon', 'icon-wallet.svg') === $icon)>{{ $label }}</option>
            @endforeach
        </select>
    </label>
    <label class="{{ $labelClass }}">Warna
        <input name="color" type="color" value="{{ old('color', '#10B981') }}" class="mt-2 h-11 w-full rounded-xl border border-slate-200 bg-white p-1.5">
    </label>
    <label class="{{ $labelClass }}">Deskripsi
        <textarea name="description" rows="4" class="form-control mt-2 h-24 py-3" placeholder="Catatan singkat">{{ old('description') }}</textarea>
    </label>
    <button class="primary-action w-full">Simpan Kategori</button>
</form>
