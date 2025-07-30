<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\WasteCollection;
use App\Models\PointRedemptions;
use App\Models\PointTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = PointTransactions::with(['user:id,name,email', 'collectionRequest.wasteBinType:id,name', 'processedBy:id,name'])
            ->where('transaction_type', 'deposit')
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $statusMap = [
                'pending' => PointTransactions::STATUS_PENDING,
                'approved' => PointTransactions::STATUS_APPROVED,
                'rejected' => PointTransactions::STATUS_REJECTED,
            ];

            if (isset($statusMap[$request->status])) {
                $query->where('status', $statusMap[$request->status]);
            }
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
                $q->where('name', 'like', '%' . $request->search . '%')->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $approvals = $query->paginate(12)->withQueryString();

        // Statistik untuk dashboard
        $stats = [
            'pending' => PointTransactions::where('transaction_type', 'deposit')->where('status', PointTransactions::STATUS_PENDING)->count(),
            'approved' => PointTransactions::where('transaction_type', 'deposit')->where('status', PointTransactions::STATUS_APPROVED)->count(),
            'rejected' => PointTransactions::where('transaction_type', 'deposit')->where('status', PointTransactions::STATUS_REJECTED)->count(),
            'total_points_pending' => PointTransactions::where('transaction_type', 'deposit')->where('status', PointTransactions::STATUS_PENDING)->sum('points'),
        ];

        return view('admin.approvals.index', compact('approvals', 'stats'));
    }

    public function edit(PointTransactions $approval)
    {
        // Pastikan ini adalah transaksi deposit
        if ($approval->transaction_type !== 'deposit') {
            return redirect()->route('admin.approvals.index')->with('error', 'Transaksi ini bukan permintaan penukaran poin.');
        }

        $approval->load(['user:id,name,email,balance', 'collectionRequest.wasteBinType:id,name', 'processedBy:id,name']);

        return view('admin.approvals.edit', compact('approval'));
    }

    public function update(Request $request, PointTransactions $approval)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        // Pastikan ini adalah transaksi deposit yang masih pending
        if ($approval->transaction_type !== 'deposit' || $approval->status !== PointTransactions::STATUS_PENDING) {
            return redirect()->route('admin.approvals.index')->with('error', 'Transaksi ini tidak dapat diproses.');
        }

        DB::beginTransaction();

        try {
            $user = $approval->user;
            $status = $request->status === 'approved' ? PointTransactions::STATUS_APPROVED : PointTransactions::STATUS_REJECTED;

            if ($request->status === 'approved') {
                // Tambahkan poin ke balance user
                $user->increment('balance', $approval->points);
                $message = 'Permintaan penukaran poin berhasil disetujui dan poin telah ditambahkan ke saldo user.';
            } else {
                $message = 'Permintaan penukaran poin telah ditolak.';
            }

            // Update status approval
            $approval->update([
                'status' => $status,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'description' => $request->admin_notes ? $approval->description . ' | Admin: ' . $request->admin_notes : $approval->description,
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

        $approvals = PointTransactions::with('user:id,balance')->whereIn('id', $request->selected_approvals)->where('transaction_type', 'deposit')->where('status', PointTransactions::STATUS_PENDING)->get();

        if ($approvals->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada transaksi yang valid untuk diproses.');
        }

        DB::beginTransaction();

        try {
            $processedCount = 0;
            $totalPoints = 0;
            $status = $request->bulk_action === 'approve' ? PointTransactions::STATUS_APPROVED : PointTransactions::STATUS_REJECTED;

            foreach ($approvals as $approval) {
                $user = $approval->user;

                if ($request->bulk_action === 'approve') {
                    // Tambahkan poin ke balance user
                    $user->increment('balance', $approval->points);
                    $totalPoints += $approval->points;
                }

                // Update approval
                $approval->update([
                    'status' => $status,
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                    'description' => $request->bulk_notes ? $approval->description . ' | Admin: ' . $request->bulk_notes : $approval->description,
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

            return redirect()
                ->back()
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
            'rejected' => PointTransactions::deposit()->rejected()->count(),
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

        if ($approval->transaction_type !== 'deposit' || $approval->status !== PointTransactions::STATUS_PENDING) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Transaksi tidak dapat diproses.',
                ],
                400,
            );
        }

        DB::beginTransaction();

        try {
            $status = $request->status === 'approved' ? PointTransactions::STATUS_APPROVED : PointTransactions::STATUS_REJECTED;

            if ($request->status === 'approved') {
                $approval->user->increment('balance', $approval->points);
            }

            $approval->update([
                'status' => $status,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses.',
                'status' => $request->status,
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses transaksi.',
                ],
                500,
            );
        }
    }

    /**
     * Show waste collections that are ready for point approval
     */
    public function showCompletedCollections(Request $request)
    {
        $query = WasteCollection::with(['user:id,name,email', 'wasteBinType:id', 'pointTransactions'])
            ->where('status', WasteCollection::STATUS_COMPLETED)
            ->whereDoesntHave('pointTransactions') // Belum ada transaksi poin
            ->orderBy('completed_at', 'desc');

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('completed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('completed_at', '<=', $request->date_to);
        }

        // Search berdasarkan nama user
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $collections = $query->paginate(12)->withQueryString();

        return view('admin.approvals.collections', compact('collections'));
    }

    /**
     * Create point transaction from completed waste collection
     */
    public function createPointTransaction(Request $request, WasteCollection $collection)
    {
        $request->validate([
            'points' => 'required|integer|min:1|max:1000',
            'description' => 'nullable|string|max:255',
        ]);

        // Pastikan collection sudah selesai dan belum ada transaksi poin
        if ($collection->status !== WasteCollection::STATUS_COMPLETED || $collection->pointTransactions()->exists()) {
            return redirect()->back()->with('error', 'Collection ini tidak dapat diproses untuk mendapat poin.');
        }

        DB::beginTransaction();

        try {
            // Buat transaksi poin baru
            PointTransactions::create([
                'user_id' => $collection->user_id,
                'waste_collection_id' => $collection->id,
                'transaction_type' => 'deposit',
                'points' => $request->points,
                'description' => $request->description ?: 'Poin dari pengumpulan sampah ' . $collection->wasteBinType->name,
                'status' => PointTransactions::STATUS_PENDING,
            ]);

            DB::commit();

            return redirect()->route('admin.approvals.index')->with('success', 'Transaksi poin berhasil dibuat dan menunggu approval.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat membuat transaksi poin: ' . $e->getMessage());
        }
    }

    public function showRedemptions(Request $request)
    {
        $query = PointRedemptions::with(['user', 'processedBy'])
            ->where('redemption_type', 'cash')
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

        // Search berdasarkan nama user atau kode redemption
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('redemption_code', 'like', "%{$search}%")->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $redemptions = $query->paginate(10);

        // Statistik untuk dashboard
        $stats = [
            'pending' => PointRedemptions::cash()->pending()->count(),
            'approved' => PointRedemptions::cash()->approved()->count(),
            'completed' => PointRedemptions::cash()->completed()->count(),
            'total_cash_pending' => PointRedemptions::cash()->pending()->sum('cash_value'),
            'total_cash_today' => PointRedemptions::cash()->whereDate('created_at', today())->sum('cash_value'),
        ];

        return view('admin.approvals.redemptions', compact('redemptions', 'stats'));
    }

    public function approveRedemption(Request $request, $id)
    {
        try {
            $redemption = PointRedemptions::findOrFail($id);

            // Validasi status
            if ($redemption->status !== PointRedemptions::STATUS_PENDING) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Penukaran ini sudah diproses sebelumnya',
                    ],
                    400,
                );
            }

            DB::transaction(function () use ($redemption, $request) {
                // Update status redemption
                $redemption->update([
                    'status' => PointRedemptions::STATUS_APPROVED,
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                    'notes' => $request->notes,
                ]);

                // Tambahkan saldo ke user
                $user = $redemption->user;
                $user->decrement('balance', $redemption->points_redeemed);

                // Kirim notifikasi ke user
                $user->notifications()->create([
                    'title' => 'Penukaran Saldo Disetujui',
                    'message' => 'Penukaran saldo sebesar Rp ' . number_format($redemption->cash_value, 0, ',', '.') . ' telah disetujui dan saldo Anda sudah bertambah.',
                    'type' => 'redemption_approved',
                    'data' => json_encode([
                        'redemption_id' => $redemption->id,
                        'redemption_code' => $redemption->redemption_code,
                        'cash_value' => $redemption->cash_value,
                    ]),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Penukaran berhasil disetujui dan saldo telah ditambahkan',
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function rejectRedemption(Request $request, $id)
    {
        try {
            $redemption = PointRedemptions::findOrFail($id);

            if ($redemption->status !== PointRedemptions::STATUS_PENDING) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Penukaran ini sudah diproses sebelumnya',
                    ],
                    400,
                );
            }

            $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            DB::transaction(function () use ($redemption, $request) {
                // Update status redemption
                $redemption->update([
                    'status' => PointRedemptions::STATUS_CANCELLED,
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                    'notes' => $request->reason,
                ]);

                // Kembalikan poin ke user (jika sudah dikurangi sebelumnya)
                // Ini tergantung implementasi sistem poin Anda

                // Kirim notifikasi ke user
                $user = $redemption->user;
                $user->notifications()->create([
                    'title' => 'Penukaran Saldo Ditolak',
                    'message' => "Penukaran saldo dengan kode {$redemption->redemption_code} ditolak. Alasan: {$request->reason}",
                    'type' => 'redemption_rejected',
                    'data' => json_encode([
                        'redemption_id' => $redemption->id,
                        'redemption_code' => $redemption->redemption_code,
                        'reason' => $request->reason,
                    ]),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Penukaran berhasil ditolak',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
