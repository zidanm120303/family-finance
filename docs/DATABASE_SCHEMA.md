# Database Schema FamFinance

## families

| Field | Type | Notes |
|---|---|---|
| id | bigint | PK |
| family_code | varchar unique | kode undangan keluarga |
| family_name | varchar | nama keluarga |
| address | text | alamat |
| city | varchar | kota |
| province | varchar | provinsi |
| postal_code | varchar | kode pos |
| phone | varchar nullable | telepon keluarga |
| created_by | foreignId nullable | user pembuat, nullable untuk menghindari circular saat seed |
| timestamps | timestamp | created_at, updated_at |

## roles

| Field | Type | Notes |
|---|---|---|
| id | bigint | PK |
| role_name | varchar unique | Kepala Keluarga, Ibu, Anak, Admin Keluarga |
| description | text nullable | deskripsi role |
| timestamps | timestamp |  |

## users

Pakai tabel users bawaan Laravel, ditambah:

- `family_id` nullable foreign key ke families.
- `role_id` nullable foreign key ke roles.
- `username` unique nullable.
- `phone` nullable.
- `photo` nullable.
- `is_active` boolean default true.
- `last_login` timestamp nullable.

## categories

- family_id
- category_name
- type enum income/expense
- icon nullable
- color nullable
- description nullable
- is_default boolean

Unique disarankan: `family_id + category_name + type`.

## wallets

- family_id
- wallet_name
- balance decimal(15,2)
- type enum cash/bank/e-wallet
- account_number nullable

## transactions

- family_id
- user_id
- category_id
- wallet_id nullable
- transaction_code unique
- type enum income/expense
- amount decimal(15,2)
- title
- description nullable
- transaction_date date
- attachment nullable
- payment_method enum cash/e-wallet/bank
- status enum pending/success/cancel

## transaction_histories

- transaction_id nullable
- user_id
- action create/update/delete
- old_data json nullable
- new_data json nullable
- note nullable
- created_at only

## budgets

- family_id
- category_id
- month integer
- year integer
- limit_amount decimal(15,2)

Unique disarankan: `family_id + category_id + month + year`.
