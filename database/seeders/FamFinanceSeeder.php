<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Family;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class FamFinanceSeeder extends Seeder
{
    public function run(): void
    {
        $this->clearExistingData();

        DB::transaction(function (): void {
            $roles = $this->createRoles();
            $family = $this->createFamily();
            $users = $this->createUsers($family, $roles);
            $family->update(['created_by' => $users->get('Budi Pratama')->id]);

            $categories = $this->createCategories($family);
            $wallets = $this->createWallets($family);

            $this->createBudgets($family, $categories);
            $this->createTransactions($family, $users, $categories, $wallets);
        });
    }

    private function clearExistingData(): void
    {
        Schema::disableForeignKeyConstraints();

        try {
            foreach ([
                'transaction_histories',
                'budgets',
                'transactions',
                'wallets',
                'categories',
                'users',
                'families',
                'roles',
            ] as $table) {
                DB::table($table)->truncate();
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    private function createRoles(): Collection
    {
        return collect([
            [
                'role_name' => 'Kepala Keluarga',
                'description' => 'Mengelola seluruh transaksi, anggaran, anggota, dan pengaturan keluarga.',
            ],
            [
                'role_name' => 'Ibu Rumah Tangga',
                'description' => 'Mengelola transaksi harian serta memantau anggaran dan laporan keluarga.',
            ],
            [
                'role_name' => 'Anak',
                'description' => 'Melihat ringkasan keluarga dengan akses pencatatan yang terbatas.',
            ],
        ])->mapWithKeys(function (array $data): array {
            $role = Role::create($data);

            return [$role->role_name => $role];
        });
    }

    private function createFamily(): Family
    {
        return Family::create([
            'family_code' => 'PRATAMA2026',
            'family_name' => 'Keluarga Pratama',
            'address' => 'Perumahan Taman Melati Blok C2 No. 8',
            'city' => 'Depok',
            'province' => 'Jawa Barat',
            'postal_code' => '16412',
            'phone' => '021-7788-2406',
        ]);
    }

    private function createUsers(Family $family, Collection $roles): Collection
    {
        $members = [
            [
                'name' => 'Budi Pratama',
                'email' => 'budi.pratama@example.com',
                'username' => 'budi.pratama',
                'phone' => '0812-3456-7810',
                'role' => 'Kepala Keluarga',
            ],
            [
                'name' => 'Siti Pratama',
                'email' => 'siti.pratama@example.com',
                'username' => 'siti.pratama',
                'phone' => '0813-2255-9042',
                'role' => 'Ibu Rumah Tangga',
            ],
            [
                'name' => 'Nara Pratama',
                'email' => 'nara.pratama@example.com',
                'username' => 'nara.pratama',
                'phone' => null,
                'role' => 'Anak',
            ],
        ];

        return collect($members)->mapWithKeys(function (array $member) use ($family, $roles): array {
            $user = User::create([
                'family_id' => $family->id,
                'role_id' => $roles->get($member['role'])->id,
                'name' => $member['name'],
                'email' => $member['email'],
                'username' => $member['username'],
                'password' => Hash::make('password'),
                'phone' => $member['phone'],
                'is_active' => true,
                'last_login' => $member['role'] === 'Kepala Keluarga' ? now() : now()->subDays(2),
            ]);

            return [$user->name => $user];
        });
    }

    private function createCategories(Family $family): Collection
    {
        $categories = [
            ['Gaji', 'income', 'icon-income.svg', '#10B981', 'Pendapatan rutin dari pekerjaan utama.'],
            ['Usaha Sampingan', 'income', 'icon-wallet.svg', '#3B82F6', 'Pendapatan katering dan pesanan rumahan.'],
            ['Bonus', 'income', 'icon-budget.svg', '#8B5CF6', 'Bonus kerja dan pendapatan tidak rutin.'],
            ['Pemasukan Lain', 'income', 'icon-income.svg', '#14B8A6', 'Cashback, bunga, atau pemasukan lainnya.'],
            ['Belanja Bulanan', 'expense', 'icon-wallet.svg', '#F59E0B', 'Sembako, kebutuhan dapur, dan perlengkapan rumah.'],
            ['Makan & Jajan', 'expense', 'icon-wallet.svg', '#F97316', 'Makan di luar, camilan, dan pesan antar.'],
            ['Transportasi', 'expense', 'icon-expense.svg', '#3B82F6', 'Bensin, parkir, tol, dan transportasi umum.'],
            ['Cicilan Rumah', 'expense', 'icon-shield.svg', '#6366F1', 'Cicilan bulanan tempat tinggal keluarga.'],
            ['Pendidikan', 'expense', 'icon-family.svg', '#8B5CF6', 'SPP, buku, seragam, dan kegiatan sekolah.'],
            ['Kesehatan', 'expense', 'icon-category-health.svg', '#EC4899', 'BPJS, obat, vitamin, dan pemeriksaan kesehatan.'],
            ['Listrik', 'expense', 'icon-lightning.svg', '#EAB308', 'Tagihan listrik rumah setiap bulan.'],
            ['Air', 'expense', 'icon-wallet.svg', '#06B6D4', 'Tagihan air dan kebutuhan sanitasi rumah.'],
            ['Internet', 'expense', 'icon-wifi.svg', '#10B981', 'Internet rumah dan paket komunikasi keluarga.'],
            ['Hiburan', 'expense', 'icon-budget.svg', '#A855F7', 'Rekreasi, streaming, dan kegiatan akhir pekan.'],
            ['Donasi', 'expense', 'icon-family.svg', '#14B8A6', 'Zakat, sedekah, dan bantuan sosial.'],
        ];

        return collect($categories)->mapWithKeys(function (array $data) use ($family): array {
            [$name, $type, $icon, $color, $description] = $data;
            $category = Category::create([
                'family_id' => $family->id,
                'category_name' => $name,
                'type' => $type,
                'icon' => $icon,
                'color' => $color,
                'description' => $description,
                'is_default' => true,
            ]);

            return ["{$type}:{$name}" => $category];
        });
    }

    private function createWallets(Family $family): Collection
    {
        $wallets = [
            ['BCA Utama', 14875000, 'bank', '**** 4821'],
            ['Jago Tabungan', 8650000, 'bank', '**** 7315'],
            ['GoPay', 685000, 'e-wallet', '0812 **** 7810'],
            ['Tunai', 920000, 'cash', null],
        ];

        return collect($wallets)->mapWithKeys(function (array $data) use ($family): array {
            [$name, $balance, $type, $accountNumber] = $data;
            $wallet = Wallet::create([
                'family_id' => $family->id,
                'wallet_name' => $name,
                'balance' => $balance,
                'type' => $type,
                'account_number' => $accountNumber,
            ]);

            return [$wallet->wallet_name => $wallet];
        });
    }

    private function createBudgets(Family $family, Collection $categories): void
    {
        $limits = [
            'Belanja Bulanan' => 3200000,
            'Makan & Jajan' => 1200000,
            'Transportasi' => 1000000,
            'Cicilan Rumah' => 3200000,
            'Pendidikan' => 1400000,
            'Kesehatan' => 750000,
            'Listrik' => 650000,
            'Air' => 200000,
            'Internet' => 375000,
            'Hiburan' => 500000,
            'Donasi' => 300000,
        ];

        foreach (range(0, 2) as $monthOffset) {
            $period = now()->startOfMonth()->subMonthsNoOverflow($monthOffset);

            foreach ($limits as $categoryName => $limit) {
                Budget::create([
                    'family_id' => $family->id,
                    'category_id' => $categories->get("expense:{$categoryName}")->id,
                    'month' => $period->month,
                    'year' => $period->year,
                    'limit_amount' => $limit,
                ]);
            }
        }
    }

    private function createTransactions(
        Family $family,
        Collection $users,
        Collection $categories,
        Collection $wallets,
    ): void {
        $sequence = 1;

        foreach (range(6, 1) as $monthOffset) {
            $period = now()->startOfMonth()->subMonthsNoOverflow($monthOffset);

            foreach ($this->pastMonthTransactions($monthOffset) as $row) {
                $this->createTransaction(
                    $family,
                    $users,
                    $categories,
                    $wallets,
                    $period,
                    $row,
                    $sequence++,
                    $period->daysInMonth,
                );
            }
        }

        $currentPeriod = now()->startOfMonth();
        foreach ($this->currentMonthTransactions() as $row) {
            $this->createTransaction(
                $family,
                $users,
                $categories,
                $wallets,
                $currentPeriod,
                $row,
                $sequence++,
                now()->day,
            );
        }
    }

    private function pastMonthTransactions(int $monthOffset): array
    {
        $groceryVariation = ($monthOffset % 3) * 125000;
        $sideIncomeVariation = ($monthOffset % 4) * 150000;
        $electricityVariation = ($monthOffset % 3) * 18000;

        $rows = [
            $this->row('Gaji Budi', 'income', 'Gaji', 12500000, 'BCA Utama', 'Budi Pratama', 'bank', 1, 'Gaji bulanan Budi.'),
            $this->row('Pendapatan katering Siti', 'income', 'Usaha Sampingan', 2050000 + $sideIncomeVariation, 'Jago Tabungan', 'Siti Pratama', 'bank', 5, 'Hasil pesanan katering rumahan.'),
            $this->row('Cicilan rumah', 'expense', 'Cicilan Rumah', 3200000, 'BCA Utama', 'Budi Pratama', 'bank', 2, 'Angsuran rumah bulanan.'),
            $this->row('Belanja kebutuhan rumah', 'expense', 'Belanja Bulanan', 2350000 + $groceryVariation, 'BCA Utama', 'Siti Pratama', 'bank', 6, 'Belanja sembako dan kebutuhan rumah satu bulan.'),
            $this->row('Uang sekolah Nara', 'expense', 'Pendidikan', 1150000, 'BCA Utama', 'Budi Pratama', 'bank', 7, 'SPP dan kegiatan sekolah Nara.'),
            $this->row('Tagihan listrik PLN', 'expense', 'Listrik', 486000 + $electricityVariation, 'BCA Utama', 'Siti Pratama', 'bank', 9, 'Pemakaian listrik rumah.'),
            $this->row('Tagihan air', 'expense', 'Air', 158000, 'BCA Utama', 'Siti Pratama', 'bank', 10, 'Tagihan air rumah.'),
            $this->row('Internet rumah', 'expense', 'Internet', 375000, 'BCA Utama', 'Budi Pratama', 'bank', 10, 'Paket internet keluarga.'),
            $this->row('BPJS keluarga', 'expense', 'Kesehatan', 480000, 'Jago Tabungan', 'Siti Pratama', 'bank', 12, 'Iuran BPJS tiga anggota keluarga.'),
            $this->row('Bensin dan tol', 'expense', 'Transportasi', 825000, 'GoPay', 'Budi Pratama', 'e-wallet', 18, 'Mobilitas kerja dan keluarga.'),
            $this->row('Makan akhir pekan', 'expense', 'Makan & Jajan', 690000, 'GoPay', 'Siti Pratama', 'e-wallet', 22, 'Makan keluarga dan pesan antar.'),
            $this->row('Langganan streaming', 'expense', 'Hiburan', 159000, 'GoPay', 'Budi Pratama', 'e-wallet', 24, 'Langganan hiburan keluarga.'),
            $this->row('Sedekah bulanan', 'expense', 'Donasi', 250000, 'Jago Tabungan', 'Siti Pratama', 'bank', 26, 'Sedekah rutin keluarga.'),
        ];

        if ($monthOffset === 3) {
            $rows[] = $this->row('Bonus proyek', 'income', 'Bonus', 2750000, 'BCA Utama', 'Budi Pratama', 'bank', 15, 'Bonus penyelesaian proyek kantor.');
        }

        return $rows;
    }

    private function currentMonthTransactions(): array
    {
        return [
            $this->row('Gaji Budi', 'income', 'Gaji', 12500000, 'BCA Utama', 'Budi Pratama', 'bank', 1, 'Gaji bulan berjalan.'),
            $this->row('Cicilan rumah', 'expense', 'Cicilan Rumah', 3200000, 'BCA Utama', 'Budi Pratama', 'bank', 1, 'Angsuran rumah bulan berjalan.'),
            $this->row('Pendapatan pesanan katering', 'income', 'Usaha Sampingan', 2400000, 'Jago Tabungan', 'Siti Pratama', 'bank', 2, 'Pelunasan pesanan katering kantor.'),
            $this->row('Belanja mingguan', 'expense', 'Belanja Bulanan', 780000, 'BCA Utama', 'Siti Pratama', 'bank', 2, 'Sayur, lauk, dan kebutuhan dapur minggu pertama.'),
            $this->row('Uang sekolah Nara', 'expense', 'Pendidikan', 1150000, 'BCA Utama', 'Budi Pratama', 'bank', 2, 'SPP dan kegiatan sekolah bulan berjalan.'),
            $this->row('Tagihan listrik PLN', 'expense', 'Listrik', 512000, 'BCA Utama', 'Siti Pratama', 'bank', 3, 'Pemakaian listrik rumah bulan lalu.'),
            $this->row('Internet rumah', 'expense', 'Internet', 375000, 'BCA Utama', 'Budi Pratama', 'bank', 3, 'Paket internet keluarga.'),
            $this->row('Sarapan keluarga', 'expense', 'Makan & Jajan', 185000, 'Tunai', 'Siti Pratama', 'cash', 4, 'Sarapan bersama di akhir pekan.'),
            $this->row('Isi bensin', 'expense', 'Transportasi', 275000, 'GoPay', 'Budi Pratama', 'e-wallet', 4, 'Isi bensin kendaraan keluarga.'),
            $this->row('Servis motor dibatalkan', 'expense', 'Transportasi', 650000, 'Jago Tabungan', 'Budi Pratama', 'bank', 4, 'Rencana servis ditunda ke bulan berikutnya.', 'cancel'),
            $this->row('Pesanan rak buku dibatalkan', 'expense', 'Hiburan', 425000, 'GoPay', 'Siti Pratama', 'e-wallet', 4, 'Pesanan dibatalkan sebelum pembayaran.', 'cancel'),
        ];
    }

    private function row(
        string $title,
        string $type,
        string $category,
        int $amount,
        string $wallet,
        string $user,
        string $method,
        int $day,
        string $description,
        string $status = 'success',
    ): array {
        return compact(
            'title',
            'type',
            'category',
            'amount',
            'wallet',
            'user',
            'method',
            'day',
            'description',
            'status',
        );
    }

    private function createTransaction(
        Family $family,
        Collection $users,
        Collection $categories,
        Collection $wallets,
        Carbon $period,
        array $row,
        int $sequence,
        int $maximumDay,
    ): void {
        $date = $period->copy()->day(min($row['day'], $maximumDay, $period->daysInMonth));
        $user = $users->get($row['user']);
        $transaction = Transaction::create([
            'family_id' => $family->id,
            'user_id' => $user->id,
            'category_id' => $categories->get("{$row['type']}:{$row['category']}")->id,
            'wallet_id' => $wallets->get($row['wallet'])->id,
            'transaction_code' => 'TRX-'.$date->format('Ym').'-'.str_pad((string) $sequence, 5, '0', STR_PAD_LEFT),
            'type' => $row['type'],
            'amount' => $row['amount'],
            'title' => $row['title'],
            'description' => $row['description'],
            'transaction_date' => $date->toDateString(),
            'payment_method' => $row['method'],
            'status' => $row['status'],
        ]);

        TransactionHistory::create([
            'transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'action' => 'create',
            'new_data' => $transaction->toArray(),
            'note' => "{$user->name} mencatat {$transaction->title}.",
            'created_at' => $date->copy()->setTime(8 + ($sequence % 10), ($sequence * 7) % 60),
        ]);
    }
}
