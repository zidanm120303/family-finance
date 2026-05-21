# Perintah WSL dari Nol sampai Jalan

> Asumsi: MariaDB sudah jalan, database sudah disiapkan bernama `fam_finance`, user DB `zidan`, port default `3306`, password kosong/default. Kalau password MariaDB Anda tidak kosong, ubah `DB_PASSWORD`.

## 1. Cek requirement

Laravel 13 membutuhkan PHP minimal 8.3. Pastikan Composer, Node, NPM tersedia.

```bash
php -v
composer -V
node -v
npm -v
mysql --version
```

## 2. Buat project Laravel 13

```bash
cd ~/projects
composer create-project --prefer-dist laravel/laravel:^13.0 finance-zidan
cd finance-zidan
```

Alternatif memakai Laravel installer:

```bash
composer global update laravel/installer
laravel new finance-zidan
cd finance-zidan
```

## 3. Setup permission storage/cache

```bash
chmod -R 775 storage bootstrap/cache
```

## 4. Copy asset pack

Misal ZIP ini diekstrak di `~/Downloads/famfinance_laravel13_pack`:

```bash
mkdir -p public/assets
cp -R ~/Downloads/famfinance_laravel13_pack/assets/png public/assets/png
cp -R ~/Downloads/famfinance_laravel13_pack/assets/svg public/assets/svg
cp -R ~/Downloads/famfinance_laravel13_pack/assets/design-tokens public/assets/design-tokens
```

## 5. Konfigurasi `.env`

```bash
cp .env.example .env
php artisan key:generate
nano .env
```

Isi bagian database:

```env
APP_NAME=FamFinance
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fam_finance
DB_USERNAME=zidan
DB_PASSWORD=
```

Kalau user `zidan` belum dibuat:

```bash
sudo mysql -u root
```

Lalu di MariaDB shell:

```sql
CREATE DATABASE IF NOT EXISTS fam_finance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'zidan'@'localhost' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON fam_finance.* TO 'zidan'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Tes koneksi:

```bash
php artisan tinker
```

Di tinker:

```php
DB::connection()->getPdo();
exit
```

## 6. Install frontend dependency

```bash
npm install
npm install -D tailwindcss @tailwindcss/vite
npm install alpinejs chart.js
```

## 7. Jalankan migrasi dan seeder

Setelah Codex membuat migration/model/seeder:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

## 8. Jalankan development server

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Buka:

```text
http://127.0.0.1:8000
```

## 9. Akun dummy seeder yang disarankan

```text
Email: budi.pratama@email.com
Password: password
Role: Kepala Keluarga
```

## 10. Command berguna selama development

```bash
php artisan optimize:clear
php artisan route:list
php artisan migrate:fresh --seed
npm run build
```
