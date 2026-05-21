# Asset Guide FamFinance

## PNG Mockup Referensi

File di `assets/png`:

1. `01_dashboard.png` — dashboard utama.
2. `02_login.png` — halaman login.
3. `03_register_family.png` — register + buat keluarga.
4. `04_transactions.png` — daftar transaksi.
5. `05_add_transaction.png` — tambah transaksi.
6. `06_categories.png` — kelola kategori + drawer detail.
7. `07_budgets.png` — halaman anggaran.
8. `08_wallets.png` — halaman dompet.
9. `09_family_members.png` — anggota keluarga.
10. `10_reports_history.png` — laporan + riwayat/audit log.

Gunakan PNG sebagai acuan layout, spacing, hierarki visual, warna, dan konten. Jangan render PNG sebagai UI final.

## SVG

File di `assets/svg` dipakai untuk logo, icon utama, icon kategori, dan ilustrasi keluarga kecil. Copy ke:

```bash
cp -R assets/svg public/assets/svg
```

Pemakaian di Blade:

```blade
<img src="{{ asset('assets/svg/logo-famfinance.svg') }}" alt="FamFinance" class="h-12">
```

## Font

Jangan embed file font. Pakai import resmi:

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
```

## Warna Utama

- Primary green: `#10B981`
- Primary dark: `#059669`
- Primary soft: `#D1FAE5`
- Ink: `#0F172A`
- Muted: `#64748B`
- Line: `#E2E8F0`
- Background: `#F8FAFC`
- Danger: `#EF4444`
- Warning: `#F59E0B`
- Blue: `#2563EB`
- Purple: `#8B5CF6`
