<?php

namespace App\Http\Controllers;

use App\Models\{Budget, Transaction, Wallet};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $familyId = $request->user()->family_id;
        $month = now()->month;
        $year = now()->year;

        $wallets = Wallet::where('family_id', $familyId)->get();
        $transactions = Transaction::with(['category', 'wallet', 'user'])
            ->where('family_id', $familyId)
            ->latest('transaction_date')
            ->limit(8)
            ->get();

        return view('pages.dashboard.index', [
            'totalBalance' => $wallets->sum('balance'),
            'incomeMonth' => Transaction::where('family_id', $familyId)->where('type', 'income')->whereMonth('transaction_date', $month)->whereYear('transaction_date', $year)->sum('amount'),
            'expenseMonth' => Transaction::where('family_id', $familyId)->where('type', 'expense')->whereMonth('transaction_date', $month)->whereYear('transaction_date', $year)->sum('amount'),
            'wallets' => $wallets,
            'transactions' => $transactions,
            'budgets' => Budget::with('category')->where('family_id', $familyId)->where('month', $month)->where('year', $year)->get(),
        ]);
    }
}
