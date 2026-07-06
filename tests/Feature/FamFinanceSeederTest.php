<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Role;
use Database\Seeders\FamFinanceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FamFinanceSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_complete_small_family_dataset(): void
    {
        $this->seed(FamFinanceSeeder::class);

        $this->assertDatabaseCount('families', 1);
        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('roles', 3);
        $this->assertDatabaseCount('categories', 15);
        $this->assertDatabaseCount('wallets', 4);
        $this->assertDatabaseCount('budgets', 33);
        $this->assertDatabaseCount('transactions', 90);
        $this->assertDatabaseCount('transaction_histories', 90);

        $headOfFamily = User::where('username', 'budi.pratama')->firstOrFail();

        $this->assertSame('Keluarga Pratama', $headOfFamily->family?->family_name);
        $this->assertSame('Kepala Keluarga', $headOfFamily->role?->role_name);
        $this->assertSame(
            ['Anak', 'Ibu Rumah Tangga', 'Kepala Keluarga'],
            Role::orderBy('role_name')->pluck('role_name')->all(),
        );
        $this->assertDatabaseMissing('roles', ['role_name' => 'Pasangan']);
        $this->assertTrue(Hash::check('password', $headOfFamily->password));
        $this->assertSame(
            14900000.0,
            (float) Transaction::query()
                ->where('status', 'success')
                ->where('type', 'income')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount'),
        );
    }
}
