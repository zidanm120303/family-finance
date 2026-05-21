# Roadmap Build Bertahap

## Phase 0 — Setup Project

- Install Laravel 13.
- Setup `.env` MariaDB.
- Install Tailwind, Alpine.js, dan Chart.js.
- Copy assets ke `public/assets`.
- Buat layout dasar Blade.

## Phase 1 — Database & Model

- Migration: families, roles, users, categories, wallets, transactions, transaction_histories, budgets.
- Seeder role default, family dummy, user dummy, kategori default, wallet dummy, budget dummy, transaksi dummy.
- Model + relationship Eloquent.

## Phase 2 — Auth Blade Minimal

- Login page.
- Register + create family page.
- Logout.
- Middleware auth.

## Phase 3 — Shell UI

- `layouts/app.blade.php`.
- Sidebar.
- Header.
- Card component.
- Badge component.
- Button component.
- Empty state dan pagination style.

## Phase 4 — Dashboard

- Summary cards.
- Cashflow chart.
- Budget progress per kategori.
- Wallet summary.
- Recent transactions.
- Spending donut chart.
- Activity list.

## Phase 5 — CRUD Transaksi

- Index transaksi + filter.
- Create/edit transaksi.
- Upload attachment ke storage.
- History otomatis create/update/delete.
- Update balance wallet saat transaksi success.

## Phase 6 — CRUD Kategori, Budget, Wallet

- Kategori dengan default/custom.
- Budget bulanan per kategori.
- Wallet cash/bank/e-wallet.
- Transfer antar dompet opsional.

## Phase 7 — Family Members & Roles

- Kelola anggota keluarga.
- Role assignment.
- Aktivasi/nonaktif user.
- Invite code sederhana memakai `family_code`.

## Phase 8 — Reports & Audit Log

- Laporan pemasukan vs pengeluaran.
- Pengeluaran per kategori.
- Cashflow bulanan.
- Audit log detail before/after.
- Export PDF/Excel bisa dibuat di phase lanjutan.
