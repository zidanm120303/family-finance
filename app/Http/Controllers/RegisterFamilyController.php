<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Family;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterFamilyController extends Controller
{
    private const ROLE_HEAD = 'kepala_keluarga';

    private const ROLE_MOTHER = 'ibu_rumah_tangga';

    private const REGISTER_ROLES = [
        self::ROLE_HEAD => 'Kepala Keluarga',
        self::ROLE_MOTHER => 'Ibu Rumah Tangga',
    ];

    public function show(): View
    {
        return view('pages.auth.register-family');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'account_role' => $request->input('account_role', self::ROLE_HEAD),
            'family_code' => $this->normalizeFamilyCode($request->input('family_code')),
        ]);

        $isHeadOfFamily = $request->input('account_role') === self::ROLE_HEAD;

        $data = $request->validate([
            'account_role' => ['required', Rule::in(array_keys(self::REGISTER_ROLES))],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:users,username'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'family_name' => [Rule::requiredIf($isHeadOfFamily), 'nullable', 'string', 'max:120'],
            'family_code' => [
                Rule::requiredIf(! $isHeadOfFamily),
                'nullable',
                'string',
                'min:4',
                'max:20',
                'regex:/^[A-Z0-9-]+$/',
                $isHeadOfFamily
                    ? Rule::unique('families', 'family_code')
                    : Rule::exists('families', 'family_code'),
            ],
            'address' => [Rule::requiredIf($isHeadOfFamily), 'nullable', 'string', 'max:500'],
            'city' => [Rule::requiredIf($isHeadOfFamily), 'nullable', 'string', 'max:80'],
            'province' => [Rule::requiredIf($isHeadOfFamily), 'nullable', 'string', 'max:80'],
            'postal_code' => [Rule::requiredIf($isHeadOfFamily), 'nullable', 'string', 'max:20'],
            'family_phone' => ['nullable', 'string', 'max:30'],
            'create_defaults' => ['nullable', 'boolean'],
        ], [
            'account_role.required' => 'Pilih peran akun terlebih dahulu.',
            'account_role.in' => 'Peran akun tidak valid.',
            'family_code.required' => 'Kode keluarga wajib diisi untuk bergabung sebagai Ibu Rumah Tangga.',
            'family_code.exists' => 'Kode keluarga tidak ditemukan.',
            'family_code.unique' => 'Kode keluarga sudah digunakan.',
            'family_code.regex' => 'Kode keluarga hanya boleh berisi huruf, angka, atau tanda hubung.',
        ]);

        $user = DB::transaction(function () use ($data) {
            $roleName = self::REGISTER_ROLES[$data['account_role']];
            $role = Role::firstOrCreate(
                ['role_name' => $roleName],
                ['description' => $this->roleDescription($roleName)]
            );

            if ($data['account_role'] === self::ROLE_MOTHER) {
                $family = Family::where('family_code', $data['family_code'])->firstOrFail();

                return User::create([
                    'family_id' => $family->id,
                    'role_id' => $role->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'username' => ($data['username'] ?? null) ?: (string) str($data['email'])->before('@'),
                    'password' => $data['password'],
                    'phone' => $data['phone'] ?? null,
                    'is_active' => true,
                    'last_login' => now(),
                ]);
            }

            $family = Family::create([
                'family_code' => ($data['family_code'] ?? null) ?: $this->generateFamilyCode(),
                'family_name' => $data['family_name'],
                'address' => $data['address'],
                'city' => $data['city'],
                'province' => $data['province'],
                'postal_code' => $data['postal_code'],
                'phone' => $data['family_phone'] ?? $data['phone'] ?? null,
            ]);

            $user = User::create([
                'family_id' => $family->id,
                'role_id' => $role->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => ($data['username'] ?? null) ?: (string) str($data['email'])->before('@'),
                'password' => $data['password'],
                'phone' => $data['phone'] ?? null,
                'is_active' => true,
                'last_login' => now(),
            ]);

            $family->update(['created_by' => $user->id]);

            if (($data['create_defaults'] ?? true)) {
                $this->createDefaults($family->id);
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    private function normalizeFamilyCode(?string $code): ?string
    {
        $normalized = strtoupper(trim((string) $code));
        $normalized = preg_replace('/[\s_]+/', '-', $normalized);
        $normalized = preg_replace('/[^A-Z0-9-]/', '', $normalized);
        $normalized = preg_replace('/-+/', '-', $normalized);
        $normalized = trim($normalized, '-');

        return $normalized !== '' ? $normalized : null;
    }

    private function generateFamilyCode(): string
    {
        do {
            $code = 'FF' . random_int(10000, 99999);
        } while (Family::where('family_code', $code)->exists());

        return $code;
    }

    private function roleDescription(string $roleName): string
    {
        return match ($roleName) {
            'Ibu Rumah Tangga' => 'Mengelola transaksi harian serta memantau anggaran dan laporan keluarga.',
            default => 'Mengelola seluruh transaksi, anggaran, anggota, dan pengaturan keluarga.',
        };
    }

    private function createDefaults(int $familyId): void
    {
        $categories = [
            ['IPL', 'expense', 'icon-wallet.svg', '#3B82F6', 'Iuran Pengelolaan Lingkungan kompleks/perumahan'],
            ['Imunisasi', 'expense', 'icon-category-health.svg', '#8B5CF6', 'Biaya imunisasi anak'],
            ['Listrik', 'expense', 'icon-lightning.svg', '#F59E0B', 'Tagihan listrik bulanan'],
            ['Internet', 'expense', 'icon-wifi.svg', '#10B981', 'Tagihan internet bulanan'],
            ['BPJS', 'expense', 'icon-shield.svg', '#3B82F6', 'Iuran BPJS Kesehatan/Ketenagakerjaan'],
            ['Asuransi', 'expense', 'icon-income.svg', '#14B8A6', 'Premi asuransi jiwa/kesehatan/properti'],
            ['Gaji', 'income', 'icon-income.svg', '#22C55E', 'Gaji bulanan'],
            ['Bonus', 'income', 'icon-budget.svg', '#8B5CF6', 'Bonus kinerja atau tahunan'],
            ['Freelance', 'income', 'icon-wallet.svg', '#3B82F6', 'Penghasilan dari pekerjaan lepas'],
            ['THR', 'income', 'icon-budget.svg', '#F59E0B', 'Tunjangan Hari Raya'],
        ];

        foreach ($categories as [$name, $type, $icon, $color, $description]) {
            Category::create([
                'family_id' => $familyId,
                'category_name' => $name,
                'type' => $type,
                'icon' => $icon,
                'color' => $color,
                'description' => $description,
                'is_default' => true,
            ]);
        }

        Wallet::create([
            'family_id' => $familyId,
            'wallet_name' => 'Cash',
            'balance' => 0,
            'type' => 'cash',
        ]);
    }
}
