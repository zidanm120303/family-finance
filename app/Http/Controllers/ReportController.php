<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionHistory;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $familyId = $request->user()->family_id;
        $periodInput = $request->input('period', now()->format('Y-m'));
        $period = Carbon::parse(strlen($periodInput) === 7 ? "{$periodInput}-01" : $periodInput);
        $from = Carbon::parse($request->input('from', $period->copy()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', $period->copy()->endOfMonth()->toDateString()));

        $transactions = Transaction::with(['category', 'wallet', 'user'])
            ->where('family_id', $familyId)
            ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
            ->when($request->category_id, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->where('status', 'success')
            ->get();

        [$yearExpression, $monthExpression] = $this->periodExpressions();
        $monthlyCashflow = Transaction::selectRaw("{$yearExpression} as year, {$monthExpression} as month, type, SUM(amount) as total")
            ->where('family_id', $familyId)
            ->where('status', 'success')
            ->whereYear('transaction_date', $to->year)
            ->groupByRaw("{$yearExpression}, {$monthExpression}, type")
            ->get();

        $expenseByCategory = $transactions
            ->where('type', 'expense')
            ->groupBy(fn (Transaction $transaction) => $transaction->category?->category_name ?? 'Lainnya')
            ->map(fn ($items) => (float) $items->sum('amount'))
            ->sortDesc();
        $historiesQuery = TransactionHistory::with(['transaction', 'user'])
            ->whereHas('user', fn ($query) => $query->where('family_id', $familyId))
            ->when($request->history_search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('note', 'like', "%{$search}%")
                        ->orWhereHas('transaction', fn ($transactionQuery) => $transactionQuery->where('title', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->history_action, fn ($query, $action) => $query->where('action', $action))
            ->latest('created_at');
        $histories = $historiesQuery->paginate(10)->withQueryString();
        $selectedHistory = TransactionHistory::with(['transaction', 'user'])
            ->whereHas('user', fn ($query) => $query->where('family_id', $familyId))
            ->when($request->history_id, fn ($query, $id) => $query->where('id', $id))
            ->latest('created_at')
            ->first();
        $monthlyLabels = collect(range(5, 0))
            ->map(fn (int $offset) => $to->copy()->subMonthsNoOverflow($offset))
            ->values();
        $monthlySeries = $monthlyLabels->map(function (Carbon $month) use ($monthlyCashflow) {
            $income = (float) $monthlyCashflow
                ->where('year', $month->year)
                ->where('month', $month->month)
                ->where('type', 'income')
                ->sum('total');
            $expense = (float) $monthlyCashflow
                ->where('year', $month->year)
                ->where('month', $month->month)
                ->where('type', 'expense')
                ->sum('total');

            return [
                'label' => $month->translatedFormat('M y'),
                'income' => $income,
                'expense' => $expense,
                'net' => $income - $expense,
            ];
        });

        return view('pages.reports.history', [
            'period' => $period,
            'from' => $from,
            'to' => $to,
            'categories' => Category::where('family_id', $familyId)->orderBy('category_name')->get(),
            'transactions' => $transactions,
            'incomeTotal' => (float) $transactions->where('type', 'income')->sum('amount'),
            'expenseTotal' => (float) $transactions->where('type', 'expense')->sum('amount'),
            'expenseByCategory' => $expenseByCategory,
            'monthlyCashflow' => $monthlyCashflow,
            'monthlySeries' => $monthlySeries,
            'histories' => $histories,
            'selectedHistory' => $selectedHistory,
        ]);
    }

    public function exportPdf(): RedirectResponse
    {
        return back()->with('success', 'Export PDF disiapkan sebagai placeholder phase lanjutan.');
    }

    public function exportExcel(): RedirectResponse
    {
        return back()->with('success', 'Export Excel disiapkan sebagai placeholder phase lanjutan.');
    }

    private function periodExpressions(): array
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => [
                "CAST(strftime('%Y', transaction_date) AS INTEGER)",
                "CAST(strftime('%m', transaction_date) AS INTEGER)",
            ],
            'pgsql' => [
                'EXTRACT(YEAR FROM transaction_date)',
                'EXTRACT(MONTH FROM transaction_date)',
            ],
            default => [
                'YEAR(transaction_date)',
                'MONTH(transaction_date)',
            ],
        };
    }
}
