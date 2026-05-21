<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $familyId = $request->user()->family_id;
        $month = (int) $request->integer('month', now()->month);
        $year = (int) $request->integer('year', now()->year);
        $period = Carbon::create($year, $month, 1);

        $budgets = Budget::with('category')
            ->where('family_id', $familyId)
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->map(function (Budget $budget) use ($familyId, $month, $year) {
                $spent = Transaction::where('family_id', $familyId)
                    ->where('category_id', $budget->category_id)
                    ->where('type', 'expense')
                    ->where('status', 'success')
                    ->whereMonth('transaction_date', $month)
                    ->whereYear('transaction_date', $year)
                    ->sum('amount');

                $limit = (float) $budget->limit_amount;
                $spent = (float) $spent;
                $remaining = $limit - $spent;
                $percentage = $limit > 0 ? round(($spent / $limit) * 100) : 0;

                return [
                    'model' => $budget,
                    'category' => $budget->category,
                    'limit' => $limit,
                    'spent' => $spent,
                    'remaining' => $remaining,
                    'percentage' => $percentage,
                    'status' => $this->budgetStatus($percentage),
                ];
            })
            ->sortBy(fn (array $budget) => $budget['category']?->category_name ?? '')
            ->values();

        $totalBudget = (float) $budgets->sum('limit');
        $totalSpent = (float) $budgets->sum('spent');
        $remainingBudget = $totalBudget - $totalSpent;
        $spentPercentage = $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 1) : 0;
        $remainingPercentage = $totalBudget > 0 ? round((max(0, $remainingBudget) / $totalBudget) * 100, 1) : 0;
        $attentionBudgets = $budgets
            ->filter(fn (array $budget) => $budget['percentage'] >= 75)
            ->sortByDesc('percentage')
            ->values();
        $months = collect(range(1, 12))
            ->mapWithKeys(fn (int $monthNumber) => [
                $monthNumber => Carbon::create($year, $monthNumber, 1)->translatedFormat('F'),
            ]);
        $years = range(now()->year - 2, now()->year + 2);

        return view('pages.budgets.index', [
            'budgets' => $budgets,
            'categories' => Category::where('family_id', $familyId)->where('type', 'expense')->orderBy('category_name')->get(),
            'month' => $month,
            'year' => $year,
            'period' => $period,
            'months' => $months,
            'years' => $years,
            'totalBudget' => $totalBudget,
            'totalSpent' => $totalSpent,
            'remainingBudget' => $remainingBudget,
            'spentPercentage' => $spentPercentage,
            'remainingPercentage' => $remainingPercentage,
            'overLimitCount' => $budgets->where('percentage', '>', 100)->count(),
            'attentionBudgets' => $attentionBudgets,
            'chartLabels' => $budgets->map(fn (array $budget) => $budget['category']?->category_name ?? 'Lainnya')->values(),
            'chartLimits' => $budgets->pluck('limit')->values(),
            'chartSpent' => $budgets->pluck('spent')->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $familyId = $request->user()->family_id;
        $data = $this->validatedData($request, $familyId);

        Budget::updateOrCreate([
            'family_id' => $familyId,
            'category_id' => $data['category_id'],
            'month' => $data['month'],
            'year' => $data['year'],
        ], [
            'limit_amount' => $data['limit_amount'],
        ]);

        return back()->with('success', 'Anggaran berhasil disimpan.');
    }

    public function update(Request $request, Budget $budget): RedirectResponse
    {
        $this->ensureFamily($request, $budget);
        $budget->update($this->validatedData($request, $request->user()->family_id));

        return back()->with('success', 'Anggaran berhasil diperbarui.');
    }

    public function destroy(Request $request, Budget $budget): RedirectResponse
    {
        $this->ensureFamily($request, $budget);
        $budget->delete();

        return back()->with('success', 'Anggaran berhasil dihapus.');
    }

    private function validatedData(Request $request, int $familyId): array
    {
        return $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')->where('family_id', $familyId)->where('type', 'expense')],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2020,2100'],
            'limit_amount' => ['required', 'numeric', 'min:1'],
        ]);
    }

    private function ensureFamily(Request $request, Budget $budget): void
    {
        abort_unless($budget->family_id === $request->user()->family_id, 404);
    }

    private function budgetStatus(float|int $percentage): array
    {
        if ($percentage >= 100) {
            return [
                'label' => 'Melebihi',
                'tone' => 'danger',
                'message' => 'Realisasi mencapai batas anggaran.',
            ];
        }

        if ($percentage >= 75 && $percentage < 100) {
            return [
                'label' => 'Waspada',
                'tone' => 'warning',
                'message' => 'Anggaran hampir mencapai batas.',
            ];
        }

        return [
            'label' => 'Aman',
            'tone' => 'safe',
            'message' => 'Pemakaian masih dalam batas.',
        ];
    }
}
