<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $totalTarget  = $goals->sum('target_amount');
        $totalSaved   = $goals->sum('current_amount');
        $totalPercent = $totalTarget > 0 ? round(($totalSaved / $totalTarget) * 100) : 0;

        return view('pages.goals.index', compact('goals', 'totalTarget', 'totalSaved', 'totalPercent'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'deadline'      => 'nullable|date',
            'emoji'         => 'nullable|string|max:10',
        ]);

        Goal::create([
            'user_id'        => auth()->id(),
            'title'          => $request->title,
            'target_amount'  => $request->target_amount,
            'current_amount' => 0,
            'deadline'       => $request->deadline,
            'emoji'          => $request->emoji ?? '🎯',
        ]);

        return back()->with('success', 'Goal berhasil ditambahkan!');
    }

    public function update(Request $request, Goal $goal)
    {
        abort_if($goal->user_id !== auth()->id(), 403);

        $request->validate([
            'current_amount' => 'required|numeric|min:0',
        ]);

        $goal->update(['current_amount' => $request->current_amount]);

        return back()->with('success', 'Tabungan berhasil diupdate!');
    }

    public function destroy(Goal $goal)
    {
        abort_if($goal->user_id !== auth()->id(), 403);
        $goal->delete();
        return back()->with('success', 'Goal berhasil dihapus!');
    }
}
