<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function index(Request $request): View
    {
        $familyId = $request->user()->family_id;
        $period = Carbon::create((int) $request->integer('year', now()->year), (int) $request->integer('month', now()->month), 1);
        $wallets = Wallet::withCount('transactions')
            ->where('family_id', $familyId)
            ->orderByRaw("CASE type WHEN 'cash' THEN 1 WHEN 'bank' THEN 2 ELSE 3 END")
            ->orderBy('wallet_name')
            ->get();
        $cashflowRows = Transaction::selectRaw('DAY(transaction_date) as day, type, SUM(amount) as total')
            ->where('family_id', $familyId)
            ->where('status', 'success')
            ->whereMonth('transaction_date', $period->month)
            ->whereYear('transaction_date', $period->year)
            ->groupBy('day', 'type')
            ->get();
        $recentActivities = Transaction::with(['wallet', 'category', 'user'])
            ->where('family_id', $familyId)
            ->whereNotNull('wallet_id')
            ->latest('transaction_date')
            ->latest('id')
            ->limit(5)
            ->get();
        $totalBalance = (float) $wallets->sum('balance');
        $bankBalance = (float) $wallets->where('type', 'bank')->sum('balance');
        $ewalletBalance = (float) $wallets->where('type', 'e-wallet')->sum('balance');

        return view('pages.wallets.index', [
            'wallets' => $wallets,
            'period' => $period,
            'cashflow' => $this->buildCashflow($cashflowRows, $period->daysInMonth),
            'recentActivities' => $recentActivities,
            'totalBalance' => $totalBalance,
            'bankBalance' => $bankBalance,
            'ewalletBalance' => $ewalletBalance,
            'bankPercentage' => $totalBalance > 0 ? round(($bankBalance / $totalBalance) * 100, 1) : 0,
            'ewalletPercentage' => $totalBalance > 0 ? round(($ewalletBalance / $totalBalance) * 100, 1) : 0,
            'bankCount' => $wallets->where('type', 'bank')->count(),
            'ewalletCount' => $wallets->where('type', 'e-wallet')->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Wallet::create($this->validatedData($request) + ['family_id' => $request->user()->family_id]);

        return back()->with('success', 'Dompet berhasil ditambahkan.');
    }

    public function update(Request $request, Wallet $wallet): RedirectResponse
    {
        $this->ensureFamily($request, $wallet);
        $wallet->update($this->validatedData($request));

        return back()->with('success', 'Dompet berhasil diperbarui.');
    }

    public function destroy(Request $request, Wallet $wallet): RedirectResponse
    {
        $this->ensureFamily($request, $wallet);

        if ($wallet->transactions()->exists()) {
            return back()->withErrors(['wallet' => 'Dompet masih dipakai transaksi.']);
        }

        $wallet->delete();

        return back()->with('success', 'Dompet berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'wallet_name' => ['required', 'string', 'max:100'],
            'balance' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:cash,bank,e-wallet'],
            'account_number' => ['nullable', 'string', 'max:80'],
        ]);
    }

    private function ensureFamily(Request $request, Wallet $wallet): void
    {
        abort_unless($wallet->family_id === $request->user()->family_id, 404);
    }

    private function buildCashflow($rows, int $days): array
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
