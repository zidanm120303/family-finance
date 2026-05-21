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
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterFamilyController extends Controller
{
    public function show(): View
    {
        return view('pages.auth.register-family');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'family_name' => ['required', 'string', 'max:120'],
            'family_code' => ['nullable', 'string', 'max:30', 'alpha_dash', 'unique:families,family_code'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:80'],
            'province' => ['required', 'string', 'max:80'],
            'postal_code' => ['required', 'string', 'max:20'],
            'create_defaults' => ['nullable', 'boolean'],
        ]);

        $user = DB::transaction(function () use ($data) {
            $role = Role::firstOrCreate(
                ['role_name' => 'Kepala Keluarga'],
                ['description' => 'Akses penuh ke semua fitur dan pengaturan.']
            );

            $family = Family::create([
                'family_code' => strtoupper($data['family_code'] ?: (string) str($data['family_name'])->slug('')->substr(0, 8).random_int(1000, 9999)),
                'family_name' => $data['family_name'],
                'address' => $data['address'],
                'city' => $data['city'],
                'province' => $data['province'],
                'postal_code' => $data['postal_code'],
                'phone' => $data['phone'] ?? null,
            ]);

            $user = User::create([
                'family_id' => $family->id,
                'role_id' => $role->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'] ?: (string) str($data['email'])->before('@'),
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
