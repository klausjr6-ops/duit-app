<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $month  = request('month', now()->month);
        $year   = request('year', now()->year);

        $monthNames = ['','Januari','Februari','Maret','April','Mei','Juni',
                       'Juli','Agustus','September','Oktober','November','Desember'];

        $transactions = Transaction::where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $totalIncome  = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance      = $totalIncome - $totalExpense;

        // Running balance
        $runningBalance = 0;
        $rows = $transactions->map(function ($t) use (&$runningBalance) {
            if ($t->type === 'income') {
                $runningBalance += $t->amount;
            } else {
                $runningBalance -= $t->amount;
            }
            return [
                'date'        => Carbon::parse($t->date)->format('d/m/Y'),
                'description' => $t->description ?? $t->category ?? '-',
                'category'    => $t->category ?? '-',
                'type'        => $t->type,
                'income'      => $t->type === 'income' ? $t->amount : null,
                'expense'     => $t->type === 'expense' ? $t->amount : null,
                'balance'     => $runningBalance,
            ];
        });

        return view('pages.reports.index', compact(
            'rows', 'totalIncome', 'totalExpense', 'balance',
            'month', 'year', 'monthNames'
        ));
    }
}
