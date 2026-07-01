<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Stats
        $totalIncome  = Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        $monthlyIncome  = Transaction::where('user_id', $userId)->where('type', 'income')->whereBetween('date', [$startOfMonth, $today])->sum('amount');
        $monthlyExpense = Transaction::where('user_id', $userId)->where('type', 'expense')->whereBetween('date', [$startOfMonth, $today])->sum('amount');

        // Last 7 days chart data
        $last7Days = collect(range(6, 0))->map(function ($i) use ($userId) {
            $date = Carbon::today()->subDays($i);
            return [
                'label'   => $date->translatedFormat('D'),
                'date'    => $date->format('Y-m-d'),
                'income'  => Transaction::where('user_id', $userId)->where('type', 'income')->whereDate('date', $date)->sum('amount'),
                'expense' => Transaction::where('user_id', $userId)->where('type', 'expense')->whereDate('date', $date)->sum('amount'),
            ];
        });

        // Budget per kategori (expense only this month)
        $budgetByCategory = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $today])
            ->whereNotNull('category')
            ->groupBy('category')
            ->selectRaw('category, SUM(amount) as total')
            ->orderByDesc('total')
            ->get();

        // Activity map (this month, per day)
        $activityMap = Transaction::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, Carbon::now()->endOfMonth()])
            ->selectRaw('date, SUM(CASE WHEN type="expense" THEN amount ELSE 0 END) as expense, SUM(CASE WHEN type="income" THEN amount ELSE 0 END) as income')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Recent transactions
        $transactions = Transaction::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.transactions.index', compact(
            'totalIncome', 'totalExpense', 'totalBalance',
            'monthlyIncome', 'monthlyExpense',
            'last7Days', 'budgetByCategory', 'activityMap', 'transactions'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'        => 'required|in:income,expense',
            'amount'      => 'required|numeric|min:1',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'date'        => 'required|date',
        ]);

        Transaction::create([
            'user_id'     => auth()->id(),
            'type'        => $request->type,
            'amount'      => $request->amount,
            'category'    => $request->category,
            'description' => $request->description,
            'date'        => $request->date,
        ]);

        return back()->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function destroy(Transaction $transaction)
    {
        abort_if($transaction->user_id !== auth()->id(), 403);
        $transaction->delete();
        return back()->with('success', 'Transaksi berhasil dihapus!');
    }
}
