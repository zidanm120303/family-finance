<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $familyId = $request->user()->family_id;
        $categories = Category::where('family_id', $familyId)
            ->withCount('transactions')
            ->orderBy('type')
            ->orderBy('category_name')
            ->get();

        return view('pages.categories.index', [
            'categories' => $categories,
            'expenseCategories' => $categories->where('type', 'expense')->values(),
            'incomeCategories' => $categories->where('type', 'income')->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $familyId = $request->user()->family_id;
        $data = $this->validatedData($request, $familyId);

        Category::create($data + ['family_id' => $familyId, 'is_default' => false]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function importDefaults(Request $request): RedirectResponse
    {
        $familyId = $request->user()->family_id;
        $created = 0;

        foreach ($this->defaultCategories() as $category) {
            $exists = Category::where('family_id', $familyId)
                ->where('type', $category['type'])
                ->where('category_name', $category['category_name'])
                ->exists();

            if ($exists) {
                continue;
            }

            Category::create($category + [
                'family_id' => $familyId,
                'is_default' => true,
            ]);

            $created++;
        }

        return back()->with(
            'success',
            $created > 0
                ? "{$created} kategori default berhasil diimport."
                : 'Semua kategori default sudah tersedia.'
        );
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->ensureFamily($request, $category);
        $category->update($this->validatedData($request, $request->user()->family_id, $category->id));

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $this->ensureFamily($request, $category);

        if ($category->is_default) {
            return back()->withErrors(['category' => 'Kategori default tidak dapat dihapus.']);
        }

        if ($category->transactions()->exists() || $category->budgets()->exists()) {
            return back()->withErrors(['category' => 'Kategori masih dipakai transaksi atau anggaran.']);
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    private function validatedData(Request $request, int $familyId, ?int $ignoreId = null): array
    {
        return $request->validate([
            'category_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'category_name')
                    ->where('family_id', $familyId)
                    ->where('type', $request->input('type'))
                    ->ignore($ignoreId),
            ],
            'type' => ['required', 'in:income,expense'],
            'icon' => ['nullable', 'string', 'max:80'],
            'color' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:300'],
        ]);
    }

    private function ensureFamily(Request $request, Category $category): void
    {
        abort_unless($category->family_id === $request->user()->family_id, 404);
    }

    private function defaultCategories(): array
    {
        return [
            [
                'category_name' => 'IPL',
                'type' => 'expense',
                'icon' => 'icon-wallet.svg',
                'color' => '#3B82F6',
                'description' => 'Iuran Pengelolaan Lingkungan kompleks/perumahan',
            ],
            [
                'category_name' => 'Imunisasi',
                'type' => 'expense',
                'icon' => 'icon-category-health.svg',
                'color' => '#8B5CF6',
                'description' => 'Biaya imunisasi anak',
            ],
            [
                'category_name' => 'Listrik',
                'type' => 'expense',
                'icon' => 'icon-lightning.svg',
                'color' => '#F59E0B',
                'description' => 'Tagihan listrik bulanan',
            ],
            [
                'category_name' => 'Internet',
                'type' => 'expense',
                'icon' => 'icon-wifi.svg',
                'color' => '#10B981',
                'description' => 'Tagihan internet bulanan',
            ],
            [
                'category_name' => 'BPJS',
                'type' => 'expense',
                'icon' => 'icon-shield.svg',
                'color' => '#3B82F6',
                'description' => 'Iuran BPJS Kesehatan/Ketenagakerjaan',
            ],
            [
                'category_name' => 'Asuransi',
                'type' => 'expense',
                'icon' => 'icon-income.svg',
                'color' => '#14B8A6',
                'description' => 'Premi asuransi jiwa/kesehatan/properti',
            ],
            [
                'category_name' => 'Gaji',
                'type' => 'income',
                'icon' => 'icon-income.svg',
                'color' => '#22C55E',
                'description' => 'Gaji bulanan',
            ],
            [
                'category_name' => 'Bonus',
                'type' => 'income',
                'icon' => 'icon-budget.svg',
                'color' => '#8B5CF6',
                'description' => 'Bonus kinerja atau tahunan',
            ],
            [
                'category_name' => 'Freelance',
                'type' => 'income',
                'icon' => 'icon-wallet.svg',
                'color' => '#3B82F6',
                'description' => 'Penghasilan dari pekerjaan lepas',
            ],
            [
                'category_name' => 'THR',
                'type' => 'income',
                'icon' => 'icon-budget.svg',
                'color' => '#F59E0B',
                'description' => 'Tunjangan Hari Raya',
            ],
        ];
    }
}
