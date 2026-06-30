<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard', [
            'todayIncome'      => 0,
            'todayExpense'     => 0,
            'monthlyIncome'    => 0,
            'monthlyExpense'   => 0,
            'totalBalance'     => 0,
            'totalSavings'     => 0,
            'todaySchedules'   => collect([]),
            'activeSchedules'  => 0,
            'todayMood'        => null,
            'todayNote'        => '',
            'financialScore'   => 50,
            'savingRate'       => 0,
            'incomeBarPct'     => 0,
            'expenseBarPct'    => 0,
        ]);
    }

    public function saveMood(Request $request)
    {
        return response()->json(['status' => 'ok']);
    }

    public function saveNote(Request $request)
    {
        return response()->json(['status' => 'ok']);
    }

    public function askAI(Request $request)
    {
        return response()->json(['reply' => 'Fitur AI segera hadir! 💰']);
    }
}
