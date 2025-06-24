<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointTransactions;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = PointTransactions::with(['user', 'wasteBinType.bin', 'processedBy'])
            ->where('transaction_type', 'deposit') // Menggunakan deposit untuk approval penukaran poin
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search berdasarkan nama user atau email
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $approvals = $query->paginate(12)->withQueryString();

        // Statistik untuk dashboard
        $stats = [
            'pending' => PointTransactions::where('transaction_type', 'deposit')
                ->where('status', 'pending')
                ->count(),
            'approved' => PointTransactions::where('transaction_type', 'deposit')
                ->where('status', 'approved')
                ->count(),
            'rejected' => PointTransactions::where('transaction_type', 'deposit')
                ->where('status', 'rejected')
                ->count(),
            'total_points_pending' => PointTransactions::where('transaction_type', 'deposit')
                ->where('status', 'pending')
                ->sum('points'),
        ];

        return view('admin.approvals.index', compact('approvals', 'stats'));
    }

    public function edit(PointTransactions $approval)
    {
        // Pastikan ini adalah transaksi deposit
        if ($approval->transaction_type !== 'deposit') {
            return redirect()->route('admin.approvals.index')
                ->with('error', 'Transaksi ini bukan permintaan penukaran poin.');
        }

        $approval->load(['user', 'wasteBinType.bin']);

        return view('admin.approvals.edit', compact('approval'));
    }

    public function update(Request $request, PointTransactions $approval)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        // Pastikan ini adalah transaksi deposit yang masih pending
        if ($approval->transaction_type !== 'deposit' || $approval->status !== 'pending') {
            return redirect()->route('admin.approvals.index')
                ->with('error', 'Transaksi ini tidak dapat diproses.');
        }

        DB::beginTransaction();

        try {
            $user = $approval->user;

            if ($request->status === 'approved') {
                // Tambahkan poin ke user (karena ini adalah deposit/penukaran sampah ke poin)
                $user->increment('balance', $approval->points);
                $message = 'Permintaan penukaran poin berhasil disetujui dan poin telah ditambahkan ke saldo user.';
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

            return redirect()->route('admin.approvals.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
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
            ->where('transaction_type', 'deposit')
            ->where('status', 'pending')
            ->get();

        if ($approvals->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada transaksi yang valid untuk diproses.');
        }

        DB::beginTransaction();

        try {
            $processedCount = 0;
            $totalPoints = 0;

            foreach ($approvals as $approval) {
                $user = $approval->user;

                if ($request->bulk_action === 'approve') {
                    // Tambahkan poin ke user
                    $user->increment('balance', $approval->points);
                    $totalPoints += $approval->points;
                    $status = 'approved';
                } else {
                    $status = 'rejected';
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

                $processedCount++;
            }

            DB::commit();

            $actionText = $request->bulk_action === 'approve' ? 'disetujui' : 'ditolak';
            $message = "Berhasil memproses {$processedCount} transaksi yang {$actionText}.";

            if ($request->bulk_action === 'approve') {
                $message .= " Total {$totalPoints} poin telah ditambahkan ke saldo user.";
            }

            return redirect()->route('admin.approvals.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses bulk approval: ' . $e->getMessage());
        }
    }

    /**
     * Get approval statistics for dashboard
     */
    public function getStats()
    {
        return [
            'pending' => PointTransactions::deposit()->pending()->count(),
            'approved' => PointTransactions::deposit()->approved()->count(),
            'rejected' => PointTransactions::deposit()->where('status', 'rejected')->count(),
            'total_points_pending' => PointTransactions::deposit()->pending()->sum('points'),
            'total_points_approved' => PointTransactions::deposit()->approved()->sum('points'),
        ];
    }

    /**
     * Quick approve/reject for AJAX requests
     */
    public function quickUpdate(Request $request, PointTransactions $approval)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        if ($approval->transaction_type !== 'deposit' || $approval->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak dapat diproses.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            if ($request->status === 'approved') {
                $approval->user->increment('balance', $approval->points);
            }

            $approval->update([
                'status' => $request->status,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses.',
                'status' => $request->status
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses transaksi.'
            ], 500);
        }
    }
}
