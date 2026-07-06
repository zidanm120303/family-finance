<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Family;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\Wallet;
use Database\Seeders\FamFinanceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(FamFinanceSeeder::class);
        $this->user = User::where('username', 'budi.pratama')->firstOrFail();
    }

    public function test_guest_and_authenticated_page_access_is_correct(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Masuk');
        $this->get(route('register-family'))->assertOk()->assertSee('Daftar Keluarga');

        foreach ([
            route('dashboard'),
            route('transactions.index'),
            route('transactions.create'),
            route('categories.index'),
            route('budgets.index'),
            route('wallets.index'),
            route('family.members'),
            route('reports.history'),
            route('settings.index'),
        ] as $protectedUrl) {
            $this->get($protectedUrl)->assertRedirect(route('login'));
        }

        $this->actingAs($this->user);
        $this->assertDatabaseMissing('roles', ['role_name' => 'Pasangan']);
        $this->assertDatabaseHas('roles', ['role_name' => 'Ibu Rumah Tangga']);

        $this->get(route('dashboard'))->assertOk()->assertSee('Ringkasan keuangan keluarga');
        $this->get(route('transactions.index'))->assertOk()->assertSee('Daftar Transaksi')->assertDontSee('Pending');
        $this->get(route('transactions.create'))->assertOk()->assertSee('Simpan Transaksi')->assertDontSee('Pending');
        $this->get(route('categories.index'))->assertOk()->assertSee('Kategori Pengeluaran');
        $this->get(route('budgets.index'))->assertOk()->assertSee('Daftar Anggaran');
        $this->get(route('wallets.index'))->assertOk()->assertSee('Daftar Dompet');
        $this->get(route('family.members'))->assertOk()->assertSee('Daftar Anggota');
        $this->get(route('reports.history'))->assertOk()->assertSee('Filter Laporan');
        $this->get(route('settings.index'))->assertOk()->assertSee('Profil Keluarga');
    }

    public function test_authentication_supports_username_email_rejection_and_logout(): void
    {
        $this->post(route('login.store'), [
            'login' => 'budi.pratama',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->user);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();

        $this->post(route('login.store'), [
            'login' => 'budi.pratama@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->user);

        auth()->logout();
        $this->post(route('login.store'), [
            'login' => 'budi.pratama',
            'password' => 'salah-password',
        ])->assertSessionHasErrors('login');
        $this->assertGuest();

        $this->user->update(['is_active' => false]);
        $this->post(route('login.store'), [
            'login' => 'budi.pratama',
            'password' => 'password',
        ])->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_authentication_upgrades_a_legacy_bcrypt_hash(): void
    {
        $legacyHash = '$2a$'.substr(Hash::make('password'), 4);

        DB::table('users')
            ->where('id', $this->user->id)
            ->update(['password' => $legacyHash]);

        $this->post(route('login.store'), [
            'login' => 'budi.pratama',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($this->user);

        $upgradedHash = $this->user->fresh()->password;

        $this->assertStringStartsWith('$2y$', $upgradedHash);
        $this->assertTrue(Hash::check('password', $upgradedHash));
    }

    public function test_family_registration_creates_a_ready_to_use_account(): void
    {
        $response = $this->post(route('register-family.store'), [
            'name' => 'Andi Raharja',
            'email' => 'andi.raharja@example.com',
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'family_name' => 'Keluarga Raharja',
            'address' => 'Jl. Anggrek No. 10',
            'city' => 'Bogor',
            'province' => 'Jawa Barat',
            'postal_code' => '16111',
            'create_defaults' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));
        $newUser = User::where('email', 'andi.raharja@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($newUser);
        $this->assertSame('andi.raharja', $newUser->username);
        $this->assertSame('Keluarga Raharja', $newUser->family?->family_name);
        $this->assertSame($newUser->id, $newUser->family?->created_by);
        $this->assertTrue(Hash::check('rahasia123', $newUser->password));
        $this->assertSame(10, $newUser->family?->categories()->count());
        $this->assertSame(1, $newUser->family?->wallets()->count());
    }

    public function test_transaction_lifecycle_keeps_wallet_and_history_consistent(): void
    {
        $this->actingAs($this->user);
        $wallet = Wallet::where('family_id', $this->user->family_id)->where('wallet_name', 'BCA Utama')->firstOrFail();
        $incomeCategory = Category::where('family_id', $this->user->family_id)->where('type', 'income')->firstOrFail();
        $expenseCategory = Category::where('family_id', $this->user->family_id)->where('type', 'expense')->firstOrFail();
        $initialBalance = (float) $wallet->balance;

        $this->post(route('transactions.store'), [
            'category_id' => $incomeCategory->id,
            'wallet_id' => $wallet->id,
            'type' => 'income',
            'amount' => 1000000,
            'title' => 'Pendapatan Pengujian',
            'description' => 'Pengujian siklus transaksi.',
            'transaction_date' => now()->toDateString(),
            'payment_method' => 'bank',
            'status' => 'success',
        ])->assertRedirect(route('transactions.index'));

        $transaction = Transaction::where('title', 'Pendapatan Pengujian')->firstOrFail();
        $this->assertSame($initialBalance + 1000000, (float) $wallet->fresh()->balance);
        $this->assertDatabaseHas('transaction_histories', [
            'transaction_id' => $transaction->id,
            'action' => 'create',
        ]);

        $this->put(route('transactions.update', $transaction), [
            'category_id' => $incomeCategory->id,
            'wallet_id' => $wallet->id,
            'type' => 'income',
            'amount' => 1000000,
            'title' => 'Pendapatan Dibatalkan',
            'description' => 'Status berubah menjadi batal.',
            'transaction_date' => now()->toDateString(),
            'payment_method' => 'bank',
            'status' => 'cancel',
        ])->assertRedirect(route('transactions.index'));

        $this->assertSame($initialBalance, (float) $wallet->fresh()->balance);

        $this->put(route('transactions.update', $transaction), [
            'category_id' => $expenseCategory->id,
            'wallet_id' => $wallet->id,
            'type' => 'expense',
            'amount' => 250000,
            'title' => 'Pengeluaran Pengujian',
            'description' => 'Status kembali sukses sebagai pengeluaran.',
            'transaction_date' => now()->toDateString(),
            'payment_method' => 'bank',
            'status' => 'success',
        ])->assertRedirect(route('transactions.index'));

        $this->assertSame($initialBalance - 250000, (float) $wallet->fresh()->balance);

        $this->delete(route('transactions.destroy', $transaction))->assertRedirect(route('transactions.index'));
        $this->assertSame($initialBalance, (float) $wallet->fresh()->balance);
        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
        $this->assertSame(94, TransactionHistory::count());
    }

    public function test_transaction_validation_and_family_isolation_are_enforced(): void
    {
        $this->actingAs($this->user);
        $incomeCategory = Category::where('family_id', $this->user->family_id)->where('type', 'income')->firstOrFail();
        $expenseCategory = Category::where('family_id', $this->user->family_id)->where('type', 'expense')->firstOrFail();
        $wallet = Wallet::where('family_id', $this->user->family_id)->firstOrFail();
        [$otherUser, $otherCategory, $otherWallet] = $this->createOtherFamily();

        $basePayload = [
            'category_id' => $incomeCategory->id,
            'wallet_id' => $wallet->id,
            'type' => 'income',
            'amount' => 100000,
            'title' => 'Validasi Transaksi',
            'transaction_date' => now()->toDateString(),
            'payment_method' => 'bank',
            'status' => 'success',
        ];

        $this->post(route('transactions.store'), array_replace($basePayload, ['status' => 'pending']))
            ->assertSessionHasErrors('status');
        $this->post(route('transactions.store'), array_replace($basePayload, [
            'category_id' => $expenseCategory->id,
        ]))->assertSessionHasErrors('category_id');
        $this->post(route('transactions.store'), array_replace($basePayload, [
            'category_id' => $otherCategory->id,
            'wallet_id' => $otherWallet->id,
        ]))->assertSessionHasErrors(['category_id', 'wallet_id']);

        $foreignTransaction = Transaction::create([
            'family_id' => $otherUser->family_id,
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
            'wallet_id' => $otherWallet->id,
            'transaction_code' => 'TRX-FOREIGN-001',
            'type' => 'income',
            'amount' => 500000,
            'title' => 'Transaksi Keluarga Lain',
            'transaction_date' => now(),
            'payment_method' => 'bank',
            'status' => 'success',
        ]);

        $this->get(route('transactions.edit', $foreignTransaction))->assertNotFound();
        $this->delete(route('transactions.destroy', $foreignTransaction))->assertNotFound();
    }

    public function test_transaction_attachment_can_be_uploaded_replaced_and_removed(): void
    {
        Storage::fake('public');
        $this->actingAs($this->user);

        $category = Category::where('family_id', $this->user->family_id)
            ->where('type', 'expense')
            ->firstOrFail();

        $this->post(route('transactions.store'), [
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 125000,
            'title' => 'Transaksi dengan Lampiran',
            'transaction_date' => now()->toDateString(),
            'payment_method' => 'cash',
            'status' => 'cancel',
            'attachment' => UploadedFile::fake()->image('struk.jpg'),
        ])->assertRedirect(route('transactions.index'));

        $transaction = Transaction::where('title', 'Transaksi dengan Lampiran')->firstOrFail();
        $firstAttachment = $transaction->attachment;
        Storage::disk('public')->assertExists($firstAttachment);

        $this->put(route('transactions.update', $transaction), [
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 125000,
            'title' => 'Transaksi dengan Lampiran',
            'transaction_date' => now()->toDateString(),
            'payment_method' => 'cash',
            'status' => 'cancel',
            'attachment' => UploadedFile::fake()->create('bukti.pdf', 24, 'application/pdf'),
        ])->assertRedirect(route('transactions.index'));

        $transaction->refresh();
        Storage::disk('public')->assertMissing($firstAttachment);
        Storage::disk('public')->assertExists($transaction->attachment);

        $latestAttachment = $transaction->attachment;
        $this->delete(route('transactions.destroy', $transaction))->assertRedirect(route('transactions.index'));
        Storage::disk('public')->assertMissing($latestAttachment);
    }

    public function test_category_budget_and_wallet_management_flows_are_correct(): void
    {
        $this->actingAs($this->user);

        $this->post(route('categories.store'), [
            'category_name' => 'Perawatan Rumah',
            'type' => 'expense',
            'icon' => 'icon-wallet.svg',
            'color' => '#334155',
            'description' => 'Biaya perawatan rumah.',
        ])->assertSessionHasNoErrors();
        $category = Category::where('category_name', 'Perawatan Rumah')->firstOrFail();

        $this->put(route('categories.update', $category), [
            'category_name' => 'Perbaikan Rumah',
            'type' => 'expense',
            'icon' => 'icon-wallet.svg',
            'color' => '#475569',
            'description' => 'Biaya perbaikan rumah.',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'category_name' => 'Perbaikan Rumah']);

        $this->post(route('budgets.store'), [
            'category_id' => $category->id,
            'month' => now()->month,
            'year' => now()->year,
            'limit_amount' => 750000,
        ])->assertSessionHasNoErrors();
        $budget = Budget::where('category_id', $category->id)->firstOrFail();
        $this->assertSame(750000.0, (float) $budget->limit_amount);

        $this->put(route('budgets.update', $budget), [
            'category_id' => $category->id,
            'month' => now()->month,
            'year' => now()->year,
            'limit_amount' => 900000,
        ])->assertSessionHasNoErrors();
        $this->assertSame(900000.0, (float) $budget->fresh()->limit_amount);

        $this->delete(route('budgets.destroy', $budget))->assertSessionHasNoErrors();
        $this->delete(route('categories.destroy', $category))->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);

        $this->post(route('wallets.store'), [
            'wallet_name' => 'Dana Darurat',
            'balance' => 2000000,
            'type' => 'bank',
            'account_number' => '**** 9900',
        ])->assertSessionHasNoErrors();
        $wallet = Wallet::where('wallet_name', 'Dana Darurat')->firstOrFail();

        $this->put(route('wallets.update', $wallet), [
            'wallet_name' => 'Tabungan Darurat',
            'balance' => 2250000,
            'type' => 'bank',
            'account_number' => '**** 9900',
        ])->assertSessionHasNoErrors();
        $this->assertSame(2250000.0, (float) $wallet->fresh()->balance);

        $this->delete(route('wallets.destroy', $wallet))->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('wallets', ['id' => $wallet->id]);

        $usedWallet = Wallet::where('family_id', $this->user->family_id)->has('transactions')->firstOrFail();
        $this->delete(route('wallets.destroy', $usedWallet))->assertSessionHasErrors('wallet');
        $defaultCategory = Category::where('family_id', $this->user->family_id)->where('is_default', true)->firstOrFail();
        $this->delete(route('categories.destroy', $defaultCategory))->assertSessionHasErrors('category');
    }

    public function test_family_member_filters_updates_and_isolation_work(): void
    {
        $this->actingAs($this->user);
        $childRole = Role::where('role_name', 'Anak')->firstOrFail();

        $this->post(route('family.members.store'), [
            'name' => 'Dina Pratama',
            'email' => 'dina.pratama@example.com',
            'username' => 'dina.pratama',
            'phone' => '081299998888',
            'role_id' => $childRole->id,
            'password' => 'password123',
        ])->assertSessionHasNoErrors();

        $member = User::where('email', 'dina.pratama@example.com')->firstOrFail();
        $this->assertSame($this->user->family_id, $member->family_id);

        $this->patch(route('family.members.update', $member), [
            'role_id' => $childRole->id,
            'is_active' => false,
        ])->assertSessionHasNoErrors();
        $this->assertFalse($member->fresh()->is_active);

        $this->get(route('family.members', ['search' => 'Dina', 'status' => '0']))
            ->assertOk()
            ->assertSee('Dina Pratama')
            ->assertDontSee('Siti Pratama');

        [$otherUser] = $this->createOtherFamily();
        $this->patch(route('family.members.update', $otherUser), [
            'role_id' => $childRole->id,
            'is_active' => false,
        ])->assertNotFound();
    }

    public function test_filters_reports_and_statuses_return_consistent_data(): void
    {
        $this->actingAs($this->user);

        $this->get(route('transactions.index', [
            'search' => 'Gaji Budi',
            'type' => 'income',
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to' => now()->endOfMonth()->toDateString(),
        ]))
            ->assertOk()
            ->assertViewHas('transactions', fn ($transactions) => $transactions->every(
                fn (Transaction $transaction) => $transaction->type === 'income'
                    && str_contains($transaction->title, 'Gaji Budi')
            ));

        $this->get(route('reports.history', [
            'period' => now()->format('Y-m'),
            'from' => now()->startOfMonth()->toDateString(),
            'to' => now()->endOfMonth()->toDateString(),
        ]))
            ->assertOk()
            ->assertViewHas('incomeTotal', 14900000.0)
            ->assertViewHas('expenseTotal', 6477000.0);

        $this->assertSame(0, Transaction::where('status', 'pending')->count());
        $this->assertSame(2, Transaction::where('status', 'cancel')->count());
        $this->assertSame(88, Transaction::where('status', 'success')->count());
    }

    private function createOtherFamily(): array
    {
        $role = Role::where('role_name', 'Kepala Keluarga')->firstOrFail();
        $family = Family::create([
            'family_code' => 'KELUARGA-LAIN',
            'family_name' => 'Keluarga Lain',
            'address' => 'Jl. Pengujian No. 2',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40111',
            'phone' => '022-1234567',
        ]);
        $user = User::create([
            'family_id' => $family->id,
            'role_id' => $role->id,
            'name' => 'Pengguna Lain',
            'email' => 'pengguna.lain@example.com',
            'username' => 'pengguna.lain',
            'password' => 'password',
            'is_active' => true,
        ]);
        $family->update(['created_by' => $user->id]);

        $category = Category::create([
            'family_id' => $family->id,
            'category_name' => 'Pendapatan Lain',
            'type' => 'income',
            'icon' => 'icon-income.svg',
            'color' => '#10B981',
            'description' => 'Kategori milik keluarga lain.',
            'is_default' => false,
        ]);
        $wallet = Wallet::create([
            'family_id' => $family->id,
            'wallet_name' => 'Bank Lain',
            'balance' => 1000000,
            'type' => 'bank',
            'account_number' => '**** 0001',
        ]);

        return [$user, $category, $wallet];
    }
}
