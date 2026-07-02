<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Schedule;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $today  = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // ── Carry Over Saldo Bulan Lalu ────────────────────
        $this->carryOverPreviousMonthBalance($userId, $startOfMonth);

        // ── Today ──────────────────────────────────────────
        $todayIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereDate('date', $today)
            ->sum('amount');

        $todayExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereDate('date', $today)
            ->sum('amount');

        // ── Monthly ────────────────────────────────────────
        $monthlyIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('date', [$startOfMonth, $today])
            ->sum('amount');

        $monthlyExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $today])
            ->sum('amount');

        // ── Total Balance ──────────────────────────────────
        $allIncome  = Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount');
        $allExpense = Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');
        $totalBalance = $allIncome - $allExpense;

        // ── Goals / Savings ────────────────────────────────
        $totalSavings = 0;
        if (class_exists('App\Models\Goal')) {
            $totalSavings = Goal::where('user_id', $userId)->sum('current_amount');
        }

        // ── Schedules ──────────────────────────────────────
        $todaySchedules  = collect([]);
        $activeSchedules = 0;
        if (class_exists('App\Models\Schedule')) {
            $todaySchedules = Schedule::where('user_id', $userId)
                ->whereDate('date', $today)
                ->orderBy('time')
                ->get();
            $activeSchedules = $todaySchedules->count();
        }

        // ── Daily Note / Mood ──────────────────────────────
        $todayMood = null;
        $todayNote = '';

        // ── Financial Health Score ─────────────────────────
        $financialScore = $this->calculateHealthScore($totalBalance, $monthlyIncome, $monthlyExpense);

        // ── Saving Rate ────────────────────────────────────
        $savingRate = $monthlyIncome > 0
            ? max(0, round((($monthlyIncome - $monthlyExpense) / $monthlyIncome) * 100))
            : 0;

        // ── Bar Percentages ────────────────────────────────
        $maxBar = max($monthlyIncome, $monthlyExpense, 1);
        $incomeBarPct  = round(($monthlyIncome  / $maxBar) * 100);
        $expenseBarPct = round(($monthlyExpense / $maxBar) * 100);

        return view('pages.dashboard', compact(
            'todayIncome', 'todayExpense',
            'monthlyIncome', 'monthlyExpense',
            'totalBalance', 'totalSavings',
            'todaySchedules', 'activeSchedules',
            'todayMood', 'todayNote',
            'financialScore', 'savingRate',
            'incomeBarPct', 'expenseBarPct'
        ));
    }

    public function saveMood(Request $request)
    {
        $request->validate(['mood' => 'required|in:ngantuk,lesu,biasa,baik,semangat']);
        return response()->json(['status' => 'ok']);
    }

    public function saveNote(Request $request)
    {
        $request->validate(['note' => 'nullable|string|max:1000']);
        return response()->json(['status' => 'ok']);
    }

    public function askAI(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $userId = auth()->id();
        $balance  = Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount')
                  - Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');
        $monthInc = Transaction::where('user_id', $userId)->where('type', 'income')->whereMonth('date', now()->month)->sum('amount');
        $monthExp = Transaction::where('user_id', $userId)->where('type', 'expense')->whereMonth('date', now()->month)->sum('amount');

        $message = strtolower($request->message);

        $replies = [
            'saldo'       => 'Saldo Anda saat ini Rp ' . number_format($balance, 0, ',', '.') . '. ' . ($balance > 0 ? 'Bagus, saldo positif! Pertahankan.' : 'Saldo negatif, segera kurangi pengeluaran.'),
            'pengeluaran' => 'Pengeluaran bulan ini Rp ' . number_format($monthExp, 0, ',', '.') . '. ' . ($monthExp > $monthInc ? 'Hati-hati, pengeluaran melebihi pemasukan!' : 'Masih dalam batas aman.'),
            'pemasukan'   => 'Pemasukan bulan ini Rp ' . number_format($monthInc, 0, ',', '.') . '. Terus tingkatkan pemasukan untuk mencapai kebebasan finansial!',
            'tabungan'    => 'Idealnya sisihkan 20% dari pemasukan untuk tabungan. Bulan ini target tabungan Anda Rp ' . number_format($monthInc * 0.2, 0, ',', '.') . '.',
            'investasi'   => 'Mulai investasi dari reksa dana pasar uang untuk pemula. Dengan saldo Rp ' . number_format($balance, 0, ',', '.') . ', Anda sudah bisa mulai!',
            'tips'        => 'Tips: Catat setiap transaksi sekecil apapun, buat anggaran bulanan, dan sisihkan tabungan di awal bulan sebelum pengeluaran lain.',
            'hutang'      => 'Prioritaskan melunasi hutang berbunga tinggi terlebih dahulu. Hindari kartu kredit jika tidak bisa melunasi tagihan penuh setiap bulan.',
        ];

        $reply = 'Maaf, saya tidak mengerti pertanyaannya. Coba tanya soal: saldo, pemasukan, pengeluaran, tabungan, investasi, hutang, atau tips keuangan!';
        foreach ($replies as $keyword => $response) {
            if (str_contains($message, $keyword)) {
                $reply = $response;
                break;
            }
        }

        return response()->json(['reply' => $reply]);
    }

    /**
     * Buat otomatis 1 transaksi pemasukan berisi saldo akhir bulan lalu,
     * dengan keterangan "Saldo Terakhir Bulan Lalu", kalau bulan ini
     * belum pernah dibuatkan entry tersebut.
     */
    private function carryOverPreviousMonthBalance(int $userId, Carbon $startOfMonth): void
    {
        $label = 'Saldo Terakhir Bulan Lalu';

        // Cek apakah bulan ini sudah pernah dibuatkan carry-over
        $alreadyExists = Transaction::where('user_id', $userId)
            ->where('description', $label)
            ->whereDate('date', '>=', $startOfMonth)
            ->exists();

        if ($alreadyExists) {
            return;
        }

        // Jangan buat carry-over kalau belum ada transaksi sama sekali
        // sebelum bulan ini (user baru / belum ada histori)
        $hasHistoryBeforeThisMonth = Transaction::where('user_id', $userId)
            ->whereDate('date', '<', $startOfMonth)
            ->exists();

        if (!$hasHistoryBeforeThisMonth) {
            return;
        }

        // Hitung saldo berjalan sampai akhir bulan lalu (semua transaksi
        // sebelum tanggal 1 bulan ini)
        $incomeBefore = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereDate('date', '<', $startOfMonth)
            ->sum('amount');

        $expenseBefore = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereDate('date', '<', $startOfMonth)
            ->sum('amount');

        $previousBalance = $incomeBefore - $expenseBefore;

        Transaction::create([
            'user_id'     => $userId,
            'type'        => 'income',
            'amount'      => $previousBalance,
            'description' => $label,
            'date'        => $startOfMonth->toDateString(),
        ]);
    }

    private function calculateHealthScore($balance, $monthlyIncome, $monthlyExpense): int
    {
        $score = 50;

        if ($balance > 0) $score += 10;
        if ($balance > 1000000) $score += 10;

        if ($monthlyIncome > 0) {
            $ratio = $monthlyExpense / $monthlyIncome;
            if ($ratio <= 0.5)      $score += 30;
            elseif ($ratio <= 0.7)  $score += 20;
            elseif ($ratio <= 0.9)  $score += 10;
            elseif ($ratio <= 1.0)  $score += 0;
            else                    $score -= 20;
        }

        return max(0, min(100, $score));
    }
}
