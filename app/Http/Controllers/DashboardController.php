<?php

namespace App\Http\Controllers;

use App\Models\{Budget, Transaction, TransactionHistory, Wallet};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $familyId = $request->user()->family_id;
        $month = (int) $request->integer('month', now()->month);
        $year = (int) $request->integer('year', now()->year);
        $period = Carbon::create($year, $month, 1);
        $previousPeriod = $period->copy()->subMonthNoOverflow();

        $wallets = Wallet::where('family_id', $familyId)->orderBy('wallet_name')->get();
        $recentTransactions = Transaction::with(['category', 'wallet', 'user'])
            ->where('family_id', $familyId)
            ->latest('transaction_date')
            ->latest('id')
            ->limit(5)
            ->get();

        $incomeMonth = Transaction::where('family_id', $familyId)
            ->where('status', 'success')
            ->where('type', 'income')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $previousIncomeMonth = Transaction::where('family_id', $familyId)
            ->where('status', 'success')
            ->where('type', 'income')
            ->whereMonth('transaction_date', $previousPeriod->month)
            ->whereYear('transaction_date', $previousPeriod->year)
            ->sum('amount');

        $previousExpenseMonth = Transaction::where('family_id', $familyId)
            ->where('status', 'success')
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $previousPeriod->month)
            ->whereYear('transaction_date', $previousPeriod->year)
            ->sum('amount');

        $expenseMonth = Transaction::where('family_id', $familyId)
            ->where('status', 'success')
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

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

                return [
                    'id' => $budget->id,
                    'category' => $budget->category,
                    'limit' => $limit,
                    'spent' => (float) $spent,
                    'percentage' => $limit > 0 ? round(((float) $spent / $limit) * 100) : 0,
                ];
            });

        $cashflowRows = Transaction::selectRaw('DAY(transaction_date) as day, type, SUM(amount) as total')
            ->where('family_id', $familyId)
            ->where('status', 'success')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->groupBy('day', 'type')
            ->get();

        $cashflow = $this->buildCashflow($cashflowRows, $period->daysInMonth);
        $expenseBreakdown = Transaction::with('category')
            ->where('family_id', $familyId)
            ->where('status', 'success')
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->get()
            ->groupBy(fn (Transaction $transaction) => $transaction->category?->category_name ?? 'Lainnya')
            ->map(fn (Collection $items) => (float) $items->sum('amount'))
            ->sortDesc();

        $totalBalance = (float) $wallets->sum('balance');
        $estimatedPreviousBalance = $totalBalance - ((float) $incomeMonth - (float) $expenseMonth);

        return view('pages.dashboard.index', [
            'totalBalance' => $totalBalance,
            'balanceChangePercentage' => $this->percentageChange($totalBalance, $estimatedPreviousBalance),
            'incomeMonth' => $incomeMonth,
            'expenseMonth' => $expenseMonth,
            'incomeChangePercentage' => $this->percentageChange((float) $incomeMonth, (float) $previousIncomeMonth),
            'expenseChangePercentage' => $this->percentageChange((float) $expenseMonth, (float) $previousExpenseMonth),
            'remainingBudget' => max(0, (float) $budgets->sum('limit') - (float) $budgets->sum('spent')),
            'budgetLimitTotal' => (float) $budgets->sum('limit'),
            'budgetSpentTotal' => (float) $budgets->sum('spent'),
            'wallets' => $wallets,
            'transactions' => $recentTransactions,
            'budgets' => $budgets,
            'cashflow' => $cashflow,
            'expenseBreakdown' => $expenseBreakdown,
            'histories' => TransactionHistory::with(['transaction', 'user'])
                ->whereHas('user', fn ($query) => $query->where('family_id', $familyId))
                ->latest('created_at')
                ->limit(5)
                ->get(),
            'period' => $period,
        ]);
    }

    private function percentageChange(float $current, float $previous): float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function buildCashflow(Collection $rows, int $days): array
    {
        $labels = range(1, $days);
        $income = array_fill(0, $days, 0);
        $expense = array_fill(0, $days, 0);

        foreach ($rows as $row) {
            $index = ((int) $row->day) - 1;
            if ($index < 0 || $index >= $days) {
                continue;
            }

            if ($row->type === 'income') {
                $income[$index] = (float) $row->total;
            }

            if ($row->type === 'expense') {
                $expense[$index] = (float) $row->total;
            }
        }

        return [
            'labels' => array_map(fn ($day) => (string) $day, $labels),
            'income' => $income,
            'expense' => $expense,
        ];
    }
}
