<?php

namespace App\Http\Controllers;

use App\Models\{Category, Transaction, TransactionHistory, Wallet};
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $familyId = $request->user()->family_id;
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        $transactions = Transaction::with(['category', 'wallet', 'user'])
            ->where('family_id', $familyId)
            ->when($request->search, fn ($q, $search) => $q->where(fn ($qq) => $qq
                ->where('title', 'like', "%{$search}%")
                ->orWhere('transaction_code', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")))
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->when($request->category_id, fn ($q, $categoryId) => $q->where('category_id', $categoryId))
            ->when($request->wallet_id, fn ($q, $walletId) => $q->where('wallet_id', $walletId))
            ->when($request->payment_method, fn ($q, $method) => $q->where('payment_method', $method))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($dateFrom, fn ($q, $date) => $q->whereDate('transaction_date', '>=', $date))
            ->when($dateTo, fn ($q, $date) => $q->whereDate('transaction_date', '<=', $date))
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $today = now();
        $periodStartMonth = $today->day > 30 ? $today->copy() : $today->copy()->subMonthNoOverflow();
        $periodEndMonth = $today->day > 30 ? $today->copy()->addMonthNoOverflow() : $today->copy();
        $periodStart = $this->cycleDateForMonth($periodStartMonth)->startOfDay();
        $periodEnd = $this->cycleDateForMonth($periodEndMonth)->endOfDay();

        $periodQuery = Transaction::where('family_id', $familyId)
            ->whereBetween('transaction_date', [$periodStart->toDateString(), $periodEnd->toDateString()]);

        $successPeriodQuery = (clone $periodQuery)->where('status', 'success');
        $incomePeriodQuery = (clone $successPeriodQuery)->where('type', 'income');
        $expensePeriodQuery = (clone $successPeriodQuery)->where('type', 'expense');
        $todayTransactionQuery = Transaction::where('family_id', $familyId)
            ->where('status', 'success')
            ->whereDate('transaction_date', $today->toDateString());

        return view('pages.transactions.index', [
            'transactions' => $transactions,
            'categories' => Category::where('family_id', $familyId)->orderBy('type')->orderBy('category_name')->get(),
            'wallets' => Wallet::where('family_id', $familyId)->orderBy('wallet_name')->get(),
            'dateRange' => $this->dateRangeValue($request, $dateFrom, $dateTo),
            'transactionSummary' => [
                'period' => $periodStart->translatedFormat('d M').' - '.$periodEnd->translatedFormat('d M'),
                'income' => [
                    'amount' => (clone $incomePeriodQuery)->sum('amount'),
                    'count' => (clone $incomePeriodQuery)->count(),
                ],
                'expense' => [
                    'amount' => (clone $expensePeriodQuery)->sum('amount'),
                    'count' => (clone $expensePeriodQuery)->count(),
                ],
                'total' => [
                    'count' => (clone $periodQuery)->count(),
                    'success_count' => (clone $successPeriodQuery)->count(),
                ],
                'today' => [
                    'amount' => (clone $todayTransactionQuery)->sum('amount'),
                    'count' => (clone $todayTransactionQuery)->count(),
                    'date' => $today->translatedFormat('d M Y'),
                ],
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $familyId = $request->user()->family_id;
        return view('pages.transactions.create', [
            'categories' => Category::where('family_id', $familyId)->orderBy('type')->orderBy('category_name')->get(),
            'wallets' => Wallet::where('family_id', $familyId)->get(),
            'transactionCode' => 'TRX-'.now()->format('Ymd').'-'.Str::upper(Str::random(5)),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $familyId = $request->user()->family_id;
        $data = $this->validatedData($request, $familyId);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('transactions', 'public');
        }

        DB::transaction(function () use ($data, $request, $familyId) {
            $transaction = Transaction::create($data + [
                'family_id' => $familyId,
                'user_id' => $request->user()->id,
                'transaction_code' => $this->generateCode(),
            ]);

            $this->applyWalletImpact($transaction, 1);

            TransactionHistory::create([
                'transaction_id' => $transaction->id,
                'user_id' => $request->user()->id,
                'action' => 'create',
                'new_data' => $transaction->fresh()->toArray(),
                'note' => 'Tambah transaksi',
                'created_at' => now(),
            ]);
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan.');
    }

    public function show(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->ensureFamily($request, $transaction);

        return redirect()->route('transactions.edit', $transaction);
    }

    public function edit(Request $request, Transaction $transaction): View
    {
        $this->ensureFamily($request, $transaction);
        $familyId = $request->user()->family_id;

        return view('pages.transactions.create', [
            'transaction' => $transaction,
            'categories' => Category::where('family_id', $familyId)->orderBy('type')->orderBy('category_name')->get(),
            'wallets' => Wallet::where('family_id', $familyId)->orderBy('wallet_name')->get(),
            'transactionCode' => $transaction->transaction_code,
        ]);
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->ensureFamily($request, $transaction);
        $familyId = $request->user()->family_id;
        $data = $this->validatedData($request, $familyId);

        if ($request->hasFile('attachment')) {
            if ($transaction->attachment) {
                Storage::disk('public')->delete($transaction->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('transactions', 'public');
        }

        DB::transaction(function () use ($transaction, $data, $request) {
            $oldData = $transaction->toArray();
            $this->applyWalletImpact($transaction, -1);

            $transaction->update($data);
            $transaction->refresh();
            $this->applyWalletImpact($transaction, 1);

            TransactionHistory::create([
                'transaction_id' => $transaction->id,
                'user_id' => $request->user()->id,
                'action' => 'update',
                'old_data' => $oldData,
                'new_data' => $transaction->toArray(),
                'note' => 'Update transaksi',
                'created_at' => now(),
            ]);
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->ensureFamily($request, $transaction);

        DB::transaction(function () use ($transaction, $request) {
            $oldData = $transaction->toArray();
            $this->applyWalletImpact($transaction, -1);

            TransactionHistory::create([
                'transaction_id' => $transaction->id,
                'user_id' => $request->user()->id,
                'action' => 'delete',
                'old_data' => $oldData,
                'note' => 'Hapus transaksi',
                'created_at' => now(),
            ]);

            if ($transaction->attachment) {
                Storage::disk('public')->delete($transaction->attachment);
            }

            $transaction->delete();
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    private function validatedData(Request $request, int $familyId): array
    {
        return $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')->where('family_id', $familyId)],
            'wallet_id' => ['nullable', Rule::exists('wallets', 'id')->where('family_id', $familyId)],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:1'],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'transaction_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,e-wallet,bank'],
            'status' => ['required', 'in:pending,success,cancel'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);
    }

    private function applyWalletImpact(Transaction $transaction, int $direction): void
    {
        if (! $transaction->wallet_id || $transaction->status !== 'success') {
            return;
        }

        $wallet = Wallet::whereKey($transaction->wallet_id)->lockForUpdate()->first();
        if (! $wallet) {
            return;
        }

        $amount = (float) $transaction->amount;
        $signedAmount = $transaction->type === 'income' ? $amount : -$amount;
        $wallet->balance = (float) $wallet->balance + ($signedAmount * $direction);
        $wallet->save();
    }

    private function ensureFamily(Request $request, Transaction $transaction): void
    {
        abort_unless($transaction->family_id === $request->user()->family_id, 404);
    }

    private function generateCode(): string
    {
        do {
            $code = 'TRX-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
        } while (Transaction::where('transaction_code', $code)->exists());

        return $code;
    }

    private function cycleDateForMonth(Carbon $date): Carbon
    {
        return $date->copy()->day(min(30, $date->daysInMonth));
    }

    private function resolveDateRange(Request $request): array
    {
        $range = trim((string) $request->input('date_range'));

        if ($range !== '') {
            $parts = preg_split('/\s+(?:-|–|—|to|sampai)\s+/i', $range);
            $dateFrom = $this->parseDateValue($parts[0] ?? null);
            $dateTo = $this->parseDateValue($parts[1] ?? null);

            return [$dateFrom, $dateTo ?: $dateFrom];
        }

        return [
            $this->parseDateValue($request->input('date_from')),
            $this->parseDateValue($request->input('date_to')),
        ];
    }

    private function dateRangeValue(Request $request, ?string $dateFrom, ?string $dateTo): string
    {
        if ($dateFrom && $dateTo) {
            return $this->formatDateForRange($dateFrom).' - '.$this->formatDateForRange($dateTo);
        }

        if ($dateFrom || $dateTo) {
            return $this->formatDateForRange($dateFrom ?: $dateTo);
        }

        return $request->filled('date_range') ? (string) $request->input('date_range') : '';
    }

    private function parseDateValue(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        foreach (['d-m-Y', 'Y-m-d', 'd/m/Y', 'Y/m/d'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);

                if ($date && $date->format($format) === $value) {
                    return $date->toDateString();
                }
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function formatDateForRange(?string $value): string
    {
        if (! $value) {
            return '';
        }

        return Carbon::parse($value)->format('d-m-Y');
    }
}
