<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Transaction;
use App\Models\Goal;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    /**
     * Endpoint yang di-poll frontend (Alpine.js) tiap X detik.
     * Mengembalikan daftar alert yang belum pernah ditampilkan.
     *
     * CATATAN: Sesuaikan nama kolom/relasi di bawah ini dengan
     * struktur tabel Schedule, Transaction, Goal, dan Budget
     * yang sebenarnya ada di project kamu.
     */
    public function pending()
    {
        $user = auth()->user();
        $alerts = [];

        // ============================================
        // 1. JADWAL YANG MENDEKATI WAKTU (30 menit ke depan)
        // ============================================
        if (class_exists(Schedule::class)) {
            $upcoming = Schedule::where('user_id', $user->id)
                ->whereBetween('scheduled_at', [now(), now()->addMinutes(30)])
                ->where('notified', false)
                ->get();

            foreach ($upcoming as $s) {
                $alerts[] = [
                    'id'    => 'schedule-' . $s->id,
                    'title' => 'Jadwal sebentar lagi',
                    'body'  => "{$s->title} — " . Carbon::parse($s->scheduled_at)->diffForHumans(),
                    'icon'  => '📅',
                ];
                $s->update(['notified' => true]);
            }
        }

        // ============================================
        // 2. BUDGET MENDEKATI / MELEWATI LIMIT
        // ============================================
        // Asumsi: user punya relasi hasMany 'budgets', kolom:
        // budgets: id, user_id, category_id, monthly_limit
        if (method_exists($user, 'budgets')) {
            $budgets = $user->budgets()->with('category')->get();

            foreach ($budgets as $budget) {
                $spent = Transaction::where('user_id', $user->id)
                    ->where('category_id', $budget->category_id)
                    ->where('type', 'keluar')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount');

                $percent = $budget->monthly_limit > 0
                    ? ($spent / $budget->monthly_limit) * 100
                    : 0;

                $categoryName = $budget->category->name ?? 'Kategori';

                if ($percent >= 100) {
                    $alerts[] = [
                        'id'    => 'budget-over-' . $budget->id . '-' . now()->format('Ym'),
                        'title' => 'Budget terlampaui!',
                        'body'  => "{$categoryName} sudah melebihi batas bulan ini",
                        'icon'  => '🚨',
                    ];
                } elseif ($percent >= 80) {
                    $alerts[] = [
                        'id'    => 'budget-warn-' . $budget->id . '-' . now()->format('Ym'),
                        'title' => 'Budget hampir habis',
                        'body'  => "{$categoryName}: " . round($percent) . "% terpakai",
                        'icon'  => '⚠️',
                    ];
                }
            }
        }

        // ============================================
        // 3. GOAL HAMPIR TERCAPAI (>= 90%)
        // ============================================
        if (class_exists(Goal::class)) {
            $goals = Goal::where('user_id', $user->id)->get();

            foreach ($goals as $goal) {
                $percent = $goal->target_amount > 0
                    ? ($goal->current_amount / $goal->target_amount) * 100
                    : 0;

                if ($percent >= 100) {
                    $alerts[] = [
                        'id'    => 'goal-done-' . $goal->id,
                        'title' => 'Goal tercapai! 🎉',
                        'body'  => "{$goal->title} sudah 100% tercapai!",
                        'icon'  => '🎉',
                    ];
                } elseif ($percent >= 90) {
                    $alerts[] = [
                        'id'    => 'goal-' . $goal->id,
                        'title' => 'Goal hampir tercapai! 🎯',
                        'body'  => "{$goal->title}: " . round($percent) . "% tercapai",
                        'icon'  => '🎯',
                    ];
                }
            }
        }

        return response()->json(['alerts' => $alerts]);
    }
}
