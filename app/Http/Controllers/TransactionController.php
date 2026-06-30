<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $totalIncome = Transaction::where('user_id', auth()->id())->where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('user_id', auth()->id())->where('type', 'expense')->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        $monthlyIncome = Transaction::where('user_id', auth()->id())
            ->where('type', 'income')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $monthlyExpense = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        return view('pages.transactions.index', compact(
            'transactions', 'totalBalance', 'totalIncome', 'totalExpense',
            'monthlyIncome', 'monthlyExpense'
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
