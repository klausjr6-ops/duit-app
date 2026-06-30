<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::where('user_id', auth()->id())
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->paginate(15);

        $todayCount = Schedule::where('user_id', auth()->id())
            ->whereDate('date', today())
            ->count();

        $upcomingCount = Schedule::where('user_id', auth()->id())
            ->whereDate('date', '>', today())
            ->count();

        return view('pages.schedules.index', compact('schedules', 'todayCount', 'upcomingCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'required|date',
            'time'        => 'nullable',
            'description' => 'nullable|string|max:500',
        ]);

        Schedule::create([
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'date'        => $request->date,
            'time'        => $request->time,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function destroy(Schedule $schedule)
    {
        abort_if($schedule->user_id !== auth()->id(), 403);
        $schedule->delete();
        return back()->with('success', 'Jadwal berhasil dihapus!');
    }
}
