# FamFinance Laravel 13 Blade Pack

Paket ini disiapkan untuk membangun aplikasi **family finance** berbasis **Laravel 13 + Blade + MySQL** secara bertahap memakai Codex.

## Isi Paket

```text
famfinance_laravel13_pack/
├─ prompts/
│  ├─ CODEX_MASTER_PROMPT.md
│  ├─ CODEX_STEP_BY_STEP_PROMPTS.md
│  └─ UI_IMPLEMENTATION_RULES.md
├─ docs/
│  ├─ WSL_COMMANDS_FROM_ZERO.md
│  ├─ DATABASE_SCHEMA.md
│  ├─ BUILD_ROADMAP.md
│  ├─ PAGE_SPECIFICATIONS.md
│  └─ ASSET_GUIDE.md
├─ assets/
│  ├─ png/                 # mockup desain semua halaman
│  ├─ svg/                 # logo, icon, ilustrasi sederhana
│  └─ design-tokens/       # warna, radius, shadow, CSS token
└─ laravel-stubs/          # file contoh untuk ditempel/dipakai Codex
```

## Target Stack

- Laravel 13
- Blade only, tanpa React/Vue/Inertia
- Vite + Tailwind CSS
- Alpine.js untuk interaksi kecil: dropdown, drawer, modal, tab, upload preview
- MySQL database: `family_finance`
- Username database disarankan: `root`
- Port MySQL default: `3306`
- Password default MySQL lokal biasanya kosong. Kalau MySQL Anda punya password, isi di `.env`.
