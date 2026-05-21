# Prompt Bertahap untuk Codex

Gunakan prompt ini satu per satu. Jangan langsung semua kalau ingin hasil rapi.

---

## Step 1 — Setup UI Foundation

Buat fondasi UI FamFinance di Laravel 13 Blade only:

- Setup Tailwind + Vite + Alpine.js + Chart.js.
- Buat `resources/views/layouts/app.blade.php`.
- Buat partial sidebar dan header.
- Buat component Blade: `card`, `button`, `badge`, `stat-card`, `progress-row`.
- Gunakan asset SVG di `public/assets/svg`.
- Import font Inter dan Plus Jakarta Sans.
- Ikuti warna dari `public/assets/design-tokens/famfinance-tokens.json`.
- Jangan gunakan React/Vue/Inertia/Livewire.

Target akhir: halaman dashboard dummy bisa tampil dengan layout shell yang mirip mockup `public/assets/png/01_dashboard.png`.

---

## Step 2 — Database, Model, Seeder

Buat migration, model, relationship, dan seeder FamFinance:

- families
- roles
- users extension
- categories
- wallets
- transactions
- transaction_histories
- budgets

Gunakan schema dari docs. Pastikan `php artisan migrate:fresh --seed` sukses.

Seeder wajib membuat:

- role: Kepala Keluarga, Ibu, Anak, Admin Keluarga;
- family: Keluarga Pratama, code PRATAMA2024;
- user: Budi Pratama, email `budi.pratama@email.com`, password `password`;
- kategori expense dan income default;
- wallet Cash, BCA, Dana, OVO;
- budget Mei 2024;
- transaksi dummy Mei 2024;
- transaction history dummy.

---

## Step 3 — Auth Blade Minimal

Buat login dan register family dengan Blade:

- Login page harus mirip `public/assets/png/02_login.png`.
- Register family page harus mirip `public/assets/png/03_register_family.png`.
- Implement login email/username.
- Implement register user + family + role Kepala Keluarga.
- Buat logout.
- Setelah login redirect ke dashboard.

---

## Step 4 — Dashboard Real Data

Buat dashboard real data berdasarkan database:

- Total saldo dari wallets.
- Pemasukan bulan ini dari transactions success income.
- Pengeluaran bulan ini dari transactions success expense.
- Sisa anggaran = total budget - expense kategori budget bulan ini.
- Chart arus kas harian dalam bulan berjalan.
- Budget progress per kategori.
- Wallet summary.
- Transaksi terbaru.
- Donut pengeluaran per kategori.
- Aktivitas terbaru dari transaction_histories.

Ikuti mockup `public/assets/png/01_dashboard.png`.

---

## Step 5 — CRUD Transaksi

Buat halaman transaksi:

- Index mirip `public/assets/png/04_transactions.png`.
- Create mirip `public/assets/png/05_add_transaction.png`.
- Edit detail.
- Filter search, tanggal, type, kategori, metode, dompet, status.
- Upload attachment JPG/PNG/PDF max 5MB.
- Saat create/update/delete, isi transaction_histories dengan old_data/new_data.
- Saat transaksi success, update saldo wallet secara aman.

---

## Step 6 — Kategori, Budget, Wallet

Buat CRUD:

- Categories page mirip `06_categories.png`, dengan tab income/expense, icon/color picker sederhana.
- Budgets page mirip `07_budgets.png`, progress bar, status Aman/Waspada/Melebihi.
- Wallets page mirip `08_wallets.png`, wallet cards, add/edit/delete wallet.

---

## Step 7 — Family Members & Roles

Buat halaman anggota keluarga mirip `09_family_members.png`:

- Ringkasan keluarga.
- Ringkasan role.
- Tabel anggota.
- Tambah anggota manual.
- Activate/deactivate user.
- Change role.
- Family code copy UI.

---

## Step 8 — Reports & Audit Log

Buat halaman laporan & riwayat mirip `10_reports_history.png`:

- Filter periode dan kategori.
- Pemasukan vs pengeluaran chart.
- Donut pengeluaran per kategori.
- Cashflow bulanan.
- Audit log table.
- Detail drawer old_data/new_data JSON pretty.
- Tombol Unduh PDF dan Export Excel boleh dummy dulu, tapi route placeholder harus ada.
