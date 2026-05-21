<?php

namespace Database\Seeders;

use App\Models\{Budget, Category, Family, Role, Transaction, TransactionHistory, User, Wallet};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FamFinanceSeeder extends Seeder
{
    public function run(): void
    {
        $roles = collect([
            ['role_name' => 'Kepala Keluarga', 'description' => 'Akses penuh ke semua fitur dan pengaturan.'],
            ['role_name' => 'Ibu', 'description' => 'Kelola transaksi, anggaran, dan laporan.'],
            ['role_name' => 'Anak', 'description' => 'Akses terbatas, dapat melihat dan input tertentu.'],
            ['role_name' => 'Admin Keluarga', 'description' => 'Kelola anggota, role, dan pengaturan keluarga.'],
        ])->map(fn ($role) => Role::firstOrCreate(['role_name' => $role['role_name']], $role));

        $family = Family::firstOrCreate([
            'family_code' => 'PRATAMA2024',
        ], [
            'family_name' => 'Keluarga Pratama',
            'address' => 'Jl. Melati No. 23, Setiabudi',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'postal_code' => '12910',
            'phone' => '021-1234567',
        ]);

        $headRole = Role::where('role_name', 'Kepala Keluarga')->first();
        $user = User::updateOrCreate([
            'email' => 'budi.pratama@email.com',
        ], [
            'family_id' => $family->id,
            'role_id' => $headRole->id,
            'name' => 'Budi Pratama',
            'username' => 'budi.pratama',
            'password' => Hash::make('password'),
            'phone' => '0812-3456-7890',
            'is_active' => true,
            'last_login' => now(),
        ]);
        $family->update(['created_by' => $user->id]);

        $expenseCategories = [
            ['IPL', 'icon-lightning.svg', '#3B82F6', 'Iuran Pengelolaan Lingkungan'],
            ['Imunisasi', 'icon-category-health.svg', '#8B5CF6', 'Biaya imunisasi anak'],
            ['Listrik', 'icon-lightning.svg', '#F59E0B', 'Tagihan listrik bulanan'],
            ['Internet', 'icon-wifi.svg', '#10B981', 'Tagihan internet bulanan'],
            ['BPJS', 'icon-shield.svg', '#3B82F6', 'Iuran BPJS Kesehatan/Ketenagakerjaan'],
            ['Asuransi', 'icon-shield.svg', '#14B8A6', 'Premi asuransi jiwa/kesehatan/properti'],
            ['Belanja Rumah Tangga', 'icon-wallet.svg', '#F59E0B', 'Belanja kebutuhan rumah'],
            ['Makanan & Minuman', 'icon-wallet.svg', '#8B5CF6', 'Makan keluarga'],
        ];
        foreach ($expenseCategories as [$name, $icon, $color, $description]) {
            Category::firstOrCreate(['family_id' => $family->id, 'category_name' => $name, 'type' => 'expense'], [
                'icon' => $icon, 'color' => $color, 'description' => $description, 'is_default' => true,
            ]);
        }

        $incomeCategories = [
            ['Gaji', 'icon-income.svg', '#22C55E', 'Gaji bulanan'],
            ['Bonus', 'icon-budget.svg', '#8B5CF6', 'Bonus kinerja atau tahunan'],
            ['Freelance', 'icon-income.svg', '#3B82F6', 'Penghasilan pekerjaan lepas'],
            ['THR', 'icon-budget.svg', '#F59E0B', 'Tunjangan Hari Raya'],
        ];
        foreach ($incomeCategories as [$name, $icon, $color, $description]) {
            Category::firstOrCreate(['family_id' => $family->id, 'category_name' => $name, 'type' => 'income'], [
                'icon' => $icon, 'color' => $color, 'description' => $description, 'is_default' => true,
            ]);
        }

        $wallets = collect([
            ['wallet_name' => 'Cash', 'balance' => 2350000, 'type' => 'cash', 'account_number' => null],
            ['wallet_name' => 'BCA', 'balance' => 12750000, 'type' => 'bank', 'account_number' => '1234 5678 9012 3456'],
            ['wallet_name' => 'Dana', 'balance' => 5180000, 'type' => 'e-wallet', 'account_number' => '0812 **** 3456'],
            ['wallet_name' => 'OVO', 'balance' => 4300000, 'type' => 'e-wallet', 'account_number' => '0812 **** 7890'],
        ])->map(fn ($wallet) => Wallet::firstOrCreate(['family_id' => $family->id, 'wallet_name' => $wallet['wallet_name']], $wallet));

        $budgetItems = [
            ['Listrik', 450000], ['Internet', 250000], ['BPJS', 150000], ['Imunisasi', 200000], ['Belanja Rumah Tangga', 4250000]
        ];
        foreach ($budgetItems as [$categoryName, $limit]) {
            $category = Category::where('family_id', $family->id)->where('category_name', $categoryName)->first();
            Budget::updateOrCreate(['family_id' => $family->id, 'category_id' => $category->id, 'month' => 5, 'year' => 2024], ['limit_amount' => $limit]);
        }

        $samples = [
            ['Gaji Bulanan', 'income', 'Gaji', 15000000, 'BCA', 'bank', 'success', '2024-05-31'],
            ['Listrik PLN', 'expense', 'Listrik', 450000, 'BCA', 'bank', 'success', '2024-05-30'],
            ['Belanja di Superindo', 'expense', 'Belanja Rumah Tangga', 320000, 'Cash', 'cash', 'success', '2024-05-29'],
            ['Internet Indihome', 'expense', 'Internet', 250000, 'BCA', 'bank', 'success', '2024-05-29'],
            ['BPJS Kesehatan', 'expense', 'BPJS', 150000, 'BCA', 'bank', 'success', '2024-05-28'],
            ['Imunisasi Anak', 'expense', 'Imunisasi', 200000, 'Cash', 'cash', 'success', '2024-05-27'],
            ['Freelance Design', 'income', 'Freelance', 1200000, 'OVO', 'e-wallet', 'success', '2024-05-25'],
        ];

        foreach ($samples as $index => [$title, $type, $categoryName, $amount, $walletName, $method, $status, $date]) {
            $transaction = Transaction::updateOrCreate([
                'transaction_code' => 'TRX-2024-'.str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT),
            ], [
                'family_id' => $family->id,
                'user_id' => $user->id,
                'category_id' => Category::where('family_id', $family->id)->where('category_name', $categoryName)->where('type', $type)->first()->id,
                'wallet_id' => Wallet::where('family_id', $family->id)->where('wallet_name', $walletName)->first()->id,
                'type' => $type,
                'amount' => $amount,
                'title' => $title,
                'description' => $title.' Mei 2024',
                'transaction_date' => $date,
                'payment_method' => $method,
                'status' => $status,
            ]);

            TransactionHistory::firstOrCreate([
                'transaction_id' => $transaction->id,
                'action' => 'create',
            ], [
                'user_id' => $user->id,
                'new_data' => $transaction->toArray(),
                'note' => 'Seeder membuat transaksi dummy',
                'created_at' => now()->subDays($index),
            ]);
        }
    }
}
