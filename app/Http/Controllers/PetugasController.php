<?php

namespace App\Http\Controllers;

use App\Models\Schedules;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    public function dashboard()
    {
        $petugasId = auth()->id();

        // Get today's schedules
        $todaySchedules = Schedules::where('petugas_id', $petugasId)
            ->whereDate('scheduled_date', today())
            ->with(['user', 'wasteBin', 'pointRedemption'])
            ->orderBy('scheduled_time')
            ->get();

        // Get upcoming schedules (next 7 days)
        $upcomingSchedules = Schedules::where('petugas_id', $petugasId)
            ->whereBetween('scheduled_date', [today()->addDay(), today()->addDays(7)])
            ->with(['user', 'wasteBin', 'pointRedemption'])
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->get();

        // Statistics
        $stats = [
            'today_total' => $todaySchedules->count(),
            'today_completed' => $todaySchedules->where('status', 'completed')->count(),
            'today_pending' => $todaySchedules->whereIn('status', ['scheduled', 'in_progress'])->count(),
            'upcoming_total' => $upcomingSchedules->count(),
            'high_priority' => $todaySchedules->where('priority', 'high')->count(),
        ];

        return view('petugas.dashboard.index', compact('todaySchedules', 'upcomingSchedules', 'stats'));
    }

    public function updateStatus(Request $request, Schedules $schedule)
    {
        $request->validate([
            'status' => 'required|in:in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:255',
        ]);

        $schedule->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        return response()->json(['success' => true, 'message' => 'Status berhasil diupdate']);
    }
}
