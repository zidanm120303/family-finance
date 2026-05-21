# CODEX MASTER PROMPT — FamFinance Laravel 13 Blade

Kamu adalah senior Laravel engineer dan UI engineer. Bangun aplikasi **FamFinance** berbasis **Laravel 13 + Blade only + MariaDB + Tailwind + Alpine.js + Chart.js**. Jangan gunakan React, Vue, Inertia, Livewire, atau SPA framework. Fokus pada Blade component, Controller, Eloquent, Migration, Seeder, dan UI yang mengikuti mockup di folder `public/assets/png`.

## Tujuan Produk

Aplikasi web untuk manajemen keuangan keluarga:

- login/register setiap anggota keluarga;
- data keluarga;
- role anggota keluarga: Kepala Keluarga, Ibu, Anak, Admin Keluarga;
- kategori pemasukan/pengeluaran;
- transaksi income/expense;
- history transaksi / audit log;
- budget bulanan;
- multi wallet/dompet/rekening;
- laporan dan dashboard interaktif.

## Stack Wajib

- Laravel 13.
- Blade only.
- MariaDB `fam_finance`.
- Tailwind CSS via Vite.
- Alpine.js hanya untuk UI interactivity kecil.
- Chart.js untuk chart.
- File upload pakai Laravel Storage public disk.

## Database Environment

Gunakan konfigurasi `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fam_finance
DB_USERNAME=root
DB_PASSWORD=
```

Jika password MariaDB lokal ada, jangan hardcode; tetap pakai `.env`.

## Asset

- Logo dan icon SVG tersedia di `public/assets/svg`.
- Mockup PNG tersedia di `public/assets/png` sebagai referensi visual saja.
- Jangan render mockup PNG sebagai tampilan final.
- Implementasikan UI dengan Blade + Tailwind + komponen reusable.
- Font: Inter dan Plus Jakarta Sans melalui Google Fonts link di layout.

## Design Direction

Ikuti gaya visual berikut:

- modern minimal flat SaaS;
- background `#F8FAFC`;
- kartu putih rounded 22–28px;
- border `#E2E8F0`;
- shadow sangat soft;
- primary green `#10B981`, dark `#059669`, soft `#D1FAE5`;
- accent blue `#2563EB`, purple `#8B5CF6`, warning `#F59E0B`, danger `#EF4444`;
- spacing lapang, rapih, teks Indonesia;
- dashboard interaktif dengan chart, progress bar, badge, table, drawer.

## Halaman Wajib

1. Login
2. Register + Create Family
3. Dashboard
4. Transaksi index
5. Tambah transaksi
6. Edit transaksi
7. Kategori index + drawer editor
8. Anggaran index
9. Dompet index
10. Anggota keluarga
11. Laporan & Riwayat
12. Pengaturan minimal

## Database Wajib

Buat migration, model, relation, seeder untuk:

- users, extend tabel bawaan Laravel;
- families;
- roles;
- categories;
- transactions;
- transaction_histories;
- budgets;
- wallets.

Gunakan schema dari `docs/DATABASE_SCHEMA.md`.

## Kualitas Kode

- Gunakan route names konsisten.
- Gunakan Form Request bila CRUD mulai kompleks.
- Gunakan policy/middleware sederhana untuk memastikan user hanya akses data `family_id` miliknya.
- Jangan membuat semua logic di Blade.
- Controller mengirim data aggregate siap pakai.
- Buat helper format rupiah bila perlu.
- Buat seeder dummy lengkap agar dashboard langsung hidup.

## Deliverable Teknis

Buat atau update file berikut secara bertahap:

- migrations;
- models;
- seeders;
- controllers;
- routes/web.php;
- resources/views/layouts/app.blade.php;
- partials/sidebar/header;
- components/card/button/badge/stat-card/progress-row/table;
- pages sesuai halaman wajib;
- resources/css/app.css;
- resources/js/app.js.

## Instruksi Penting

Kerjakan bertahap. Setelah setiap phase, pastikan:

```bash
php artisan migrate:fresh --seed
npm run build
php artisan route:list
```

Tidak boleh ada error fatal. Kalau ada file bawaan Laravel yang perlu dipertahankan, modifikasi dengan hati-hati.
