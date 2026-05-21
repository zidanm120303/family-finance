# PR Task List untuk Codex

Gunakan checklist ini sebagai target pull request bertahap.

## PR 01 — Foundation

- [ ] Setup Laravel 13 dengan Blade only.
- [ ] Setup Tailwind, Alpine, Chart.js.
- [ ] Copy assets ke `public/assets`.
- [ ] App shell: layout, sidebar, header.
- [ ] Komponen: card, button, badge, stat-card, progress-row.

## PR 02 — Database

- [ ] Migration schema lengkap.
- [ ] Model + relationship.
- [ ] Seeder dummy lengkap.
- [ ] `php artisan migrate:fresh --seed` sukses.

## PR 03 — Auth

- [ ] Login page.
- [ ] Register + create family.
- [ ] Auth controller.
- [ ] Logout.

## PR 04 — Dashboard

- [ ] Dashboard real data.
- [ ] Chart cashflow.
- [ ] Donut expense.
- [ ] Recent transactions.
- [ ] Budget and wallet widgets.

## PR 05 — Transactions

- [ ] Transactions index + filters.
- [ ] Create/edit/delete transaction.
- [ ] Upload attachment.
- [ ] Audit log.
- [ ] Wallet balance adjustment.

## PR 06 — Management Pages

- [ ] Categories CRUD.
- [ ] Budgets CRUD.
- [ ] Wallets CRUD.

## PR 07 — Family & Reports

- [ ] Members management.
- [ ] Roles management minimal.
- [ ] Reports and audit log detail drawer.
