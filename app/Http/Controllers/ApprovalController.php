<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
public function index(Request $request)
    {
        $query = PointTransactions::with(['user', 'wasteBinType'])
            ->where('transaction_type', 'withdrawal')
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default tampilkan yang pending
            $query->where('status', 'pending');
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search berdasarkan nama user
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $approvals = $query->paginate(15)->withQueryString();

        // Statistik untuk dashboard
        $stats = [
            'pending' => PointTransactions::where('transaction_type', 'withdrawal')->where('status', 'pending')->count(),
            'approved' => PointTransactions::where('transaction_type', 'withdrawal')->where('status', 'approved')->count(),
            'rejected' => PointTransactions::where('transaction_type', 'withdrawal')->where('status', 'rejected')->count(),
            'total_points_pending' => PointTransactions::where('transaction_type', 'withdrawal')->where('status', 'pending')->sum('points'),
        ];

        return view('admin.approvals.index', compact('approvals', 'stats'));
    }

    public function edit(PointTransactions $approval)
    {
        // Pastikan ini adalah transaksi withdrawal
        if ($approval->transaction_type !== 'withdrawal') {
            return redirect()->route('admin.approvals.index')
                ->with('error', 'Transaksi ini bukan permintaan penukaran poin.');
        }

        $approval->load(['user', 'wasteBinType']);

        return view('admin.approvals.edit', compact('approval'));
    }

    public function update(Request $request, PointTransactions $approval)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        // Pastikan ini adalah transaksi withdrawal yang masih pending
        if ($approval->transaction_type !== 'withdrawal' || $approval->status !== 'pending') {
            return redirect()->route('admin.approvals.index')
                ->with('error', 'Transaksi ini tidak dapat diproses.');
        }

        DB::beginTransaction();

        try {
            $user = $approval->user;

            if ($request->status === 'approved') {
                // Cek apakah user memiliki poin yang cukup
                if ($user->points < $approval->points) {
                    return redirect()->back()
                        ->with('error', 'User tidak memiliki cukup poin untuk transaksi ini.');
                }

                // Kurangi poin user
                $user->decrement('points', $approval->points);

                $message = 'Permintaan penukaran poin berhasil disetujui.';
            } else {
                $message = 'Permintaan penukaran poin telah ditolak.';
            }

            // Update status approval
            $approval->update([
                'status' => $request->status,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'description' => $request->admin_notes ?
                    $approval->description . ' | Admin: ' . $request->admin_notes :
                    $approval->description,
            ]);

            DB::commit();

            return redirect()->route('admin.approvals.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses approval: ' . $e->getMessage());
        }
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'selected_approvals' => 'required|array|min:1',
            'selected_approvals.*' => 'exists:point_transactions,id',
            'bulk_action' => 'required|in:approve,reject',
            'bulk_notes' => 'nullable|string|max:500',
        ]);

        $approvals = PointTransactions::whereIn('id', $request->selected_approvals)
            ->where('transaction_type', 'withdrawal')
            ->where('status', 'pending')
            ->get();

        if ($approvals->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada transaksi yang valid untuk diproses.');
        }

        DB::beginTransaction();

        try {
            $processedCount = 0;
            $failedCount = 0;

            foreach ($approvals as $approval) {
                $user = $approval->user;

                if ($request->bulk_action === 'approve') {
                    // Cek poin user
                    if ($user->points >= $approval->points) {
                        $user->decrement('points', $approval->points);
                        $status = 'approved';
                        $processedCount++;
                    } else {
                        $failedCount++;
                        continue;
                    }
                } else {
                    $status = 'rejected';
                    $processedCount++;
                }

                // Update approval
                $approval->update([
                    'status' => $status,
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                    'description' => $request->bulk_notes ?
                        $approval->description . ' | Admin: ' . $request->bulk_notes :
                        $approval->description,
                ]);
            }

            DB::commit();

            $message = "Berhasil memproses {$processedCount} transaksi.";
            if ($failedCount > 0) {
                $message .= " {$failedCount} transaksi gagal karena poin tidak mencukupi.";
            }

            return redirect()->route('admin.approvals.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses bulk approval.');
        }
    }
}
