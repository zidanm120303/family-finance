<?php

namespace App\Http\Controllers;

use App\Models\{Category, Transaction, TransactionHistory, Wallet};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $familyId = $request->user()->family_id;
        $transactions = Transaction::with(['category', 'wallet', 'user'])
            ->where('family_id', $familyId)
            ->when($request->search, fn ($q) => $q->where(fn ($qq) => $qq->where('title', 'like', "%{$request->search}%")->orWhere('transaction_code', 'like', "%{$request->search}%")))
            ->latest('transaction_date')
            ->paginate(12);

        return view('pages.transactions.index', compact('transactions'));
    }

    public function create(Request $request)
    {
        $familyId = $request->user()->family_id;
        return view('pages.transactions.create', [
            'categories' => Category::where('family_id', $familyId)->orderBy('type')->orderBy('category_name')->get(),
            'wallets' => Wallet::where('family_id', $familyId)->get(),
            'transactionCode' => 'TRX-'.now()->format('Ymd').'-'.Str::upper(Str::random(5)),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'wallet_id' => ['nullable', 'exists:wallets,id'],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0'],
            'title' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:250'],
            'transaction_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,e-wallet,bank'],
            'status' => ['required', 'in:pending,success,cancel'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('transactions', 'public');
        }

        $transaction = Transaction::create($data + [
            'family_id' => $request->user()->family_id,
            'user_id' => $request->user()->id,
            'transaction_code' => 'TRX-'.now()->format('Ymd').'-'.Str::upper(Str::random(5)),
        ]);

        TransactionHistory::create([
            'transaction_id' => $transaction->id,
            'user_id' => $request->user()->id,
            'action' => 'create',
            'new_data' => $transaction->toArray(),
            'note' => 'Tambah transaksi',
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan.');
    }
}
