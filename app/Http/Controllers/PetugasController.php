<?php

namespace App\Http\Controllers;

use App\Models\Schedules;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\WasteCollection;
use App\Models\PointTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PetugasController extends Controller
{
    public function dashboard()
    {
        $petugasId = auth()->id();

        // Get today's collections
        $todayCollections = WasteCollection::where('assigned_to', $petugasId)
            ->whereDate('pickup_date', today())
            ->with(['user', 'wasteBinType'])
            ->orderBy('pickup_time')
            ->get();

        // Get upcoming collections (next 7 days)
        $upcomingCollections = WasteCollection::where('assigned_to', $petugasId)
            ->whereBetween('pickup_date', [today()->addDay(), today()->addDays(7)])
            ->with(['user', 'wasteBinType'])
            ->orderBy('pickup_date')
            ->orderBy('pickup_time')
            ->get();

        // Statistics
        $stats = [
            'today_total' => $todayCollections->count(),
            'today_completed' => $todayCollections->where('status', WasteCollection::STATUS_COMPLETED)->count(),
            'today_pending' => $todayCollections->whereIn('status', [WasteCollection::STATUS_SCHEDULED, WasteCollection::STATUS_IN_PROGRESS])->count(),
            'upcoming_total' => $upcomingCollections->count(),
            'high_priority' => $todayCollections->where('status', WasteCollection::STATUS_IN_PROGRESS)->count(),
        ];

        return view('petugas.dashboard.index', compact('todayCollections', 'upcomingCollections', 'stats'));
    }

    public function showTask($id)
    {
        // Ambil waste collection berdasarkan ID dengan relasi yang diperlukan
        $wasteCollection = WasteCollection::with(['user', 'wasteBinType'])->findOrFail($id);

        // Pastikan hanya petugas yang ditugaskan yang bisa melihat
        if ($wasteCollection->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Pastikan status masih bisa diproses
        if (!in_array($wasteCollection->status, [WasteCollection::STATUS_SCHEDULED, WasteCollection::STATUS_IN_PROGRESS])) {
            return redirect()->route('petugas.dashboard')->with('error', 'Pengambilan sampah ini sudah tidak dapat diproses');
        }

        // Update status menjadi in_progress jika masih scheduled
        if ($wasteCollection->status === WasteCollection::STATUS_SCHEDULED) {
            $wasteCollection->update(['status' => WasteCollection::STATUS_IN_PROGRESS]);
        }

        // Ambil jenis sampah yang diminta
        $requestedWasteTypes = $wasteCollection->waste_types ?? [];

        return view('petugas.dashboard.edit', compact('wasteCollection', 'requestedWasteTypes'));
    }

    public function updateTask(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'berat_kertas' => 'nullable|numeric|min:0|max:999.9',
            'berat_plastik' => 'nullable|numeric|min:0|max:999.9',
            'berat_kardus' => 'nullable|numeric|min:0|max:999.9',
            'catatan' => 'nullable|string|max:1000',
        ]);

        // Ambil waste collection
        $wasteCollection = WasteCollection::with(['user'])->findOrFail($id);

        // Pastikan hanya petugas yang ditugaskan yang bisa update
        if ($wasteCollection->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Pastikan status masih bisa diproses
        if (!in_array($wasteCollection->status, [WasteCollection::STATUS_SCHEDULED, WasteCollection::STATUS_IN_PROGRESS])) {
            return redirect()->route('petugas.dashboard')->with('error', 'Pengambilan sampah ini sudah tidak dapat diproses');
        }

        $beratKertas = (float) ($request->berat_kertas ?? 0);
        $beratPlastik = (float) ($request->berat_plastik ?? 0);
        $beratKardus = (float) ($request->berat_kardus ?? 0);

        // Validasi minimal ada satu jenis sampah
        if ($beratKertas <= 0 && $beratPlastik <= 0 && $beratKardus <= 0) {
            return back()->with('error', 'Harap masukkan minimal satu jenis sampah yang diambil!');
        }

        try {
            DB::beginTransaction();

            // Hitung poin berdasarkan jenis sampah
            $poinKertas = $beratKertas * 15; // 15 poin per kg
            $poinPlastik = $beratPlastik * 10; // 10 poin per kg
            $poinKardus = $beratKardus * 12; // 12 poin per kg
            $totalPoin = $poinKertas + $poinPlastik + $poinKardus;

            // Buat point transactions untuk setiap jenis sampah yang diambil dengan status PENDING
            if ($beratKertas > 0) {
                PointTransactions::create([
                    'user_id' => $wasteCollection->user_id,
                    'waste_collection_id' => $wasteCollection->id,
                    'transaction_type' => 'deposit',
                    'points' => $poinKertas,
                    'percentage_deposited' => $beratKertas, // Simpan berat sebagai percentage_deposited
                    'description' => "Pengambilan sampah kertas: {$beratKertas} kg ({$poinKertas} poin)",
                    'status' => PointTransactions::STATUS_PENDING, // STATUS PENDING untuk approval
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);
            }

            if ($beratPlastik > 0) {
                PointTransactions::create([
                    'user_id' => $wasteCollection->user_id,
                    'waste_collection_id' => $wasteCollection->id,
                    'transaction_type' => 'deposit',
                    'points' => $poinPlastik,
                    'percentage_deposited' => $beratPlastik,
                    'description' => "Pengambilan sampah plastik: {$beratPlastik} kg ({$poinPlastik} poin)",
                    'status' => PointTransactions::STATUS_PENDING, // STATUS PENDING untuk approval
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);
            }

            if ($beratKardus > 0) {
                PointTransactions::create([
                    'user_id' => $wasteCollection->user_id,
                    'waste_collection_id' => $wasteCollection->id,
                    'transaction_type' => 'deposit',
                    'points' => $poinKardus,
                    'percentage_deposited' => $beratKardus,
                    'description' => "Pengambilan sampah kardus: {$beratKardus} kg ({$poinKardus} poin)",
                    'status' => PointTransactions::STATUS_PENDING, // STATUS PENDING untuk approval
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);
            }

            // Update status waste collection menjadi COMPLETED
            $wasteCollection->update([
                'status' => WasteCollection::STATUS_COMPLETED, // Ubah ke COMPLETED
                'notes' => $request->catatan,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'completed_at' => now(),
            ]);

            // Kirim notifikasi ke admin untuk approval
            $this->sendAdminNotification($wasteCollection, $totalPoin, $beratKertas, $beratPlastik, $beratKardus);

            // Kirim notifikasi ke user bahwa sampaah sudah diambil
            $this->sendUserNotification($wasteCollection, $totalPoin);

            DB::commit();

            return redirect()
                ->route('petugas.dashboard')
                ->with('success', "Pengambilan sampah berhasil diselesaikan! Total {$totalPoin} poin menunggu approval admin.");
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Terjadi kesalahan saat memproses data: ' . $e->getMessage());
        }
    }

    private function sendAdminNotification($wasteCollection, $totalPoin, $beratKertas, $beratPlastik, $beratKardus = 0)
    {
        // Ambil semua admin/petugas_pusat
        $admins = \App\Models\User::role('petugas_pusat')->get();

        $details = [];
        if ($beratKertas > 0) {
            $details[] = "Kertas: {$beratKertas} kg (" . ($beratKertas * 15) . ' poin)';
        }
        if ($beratPlastik > 0) {
            $details[] = "Plastik: {$beratPlastik} kg (" . ($beratPlastik * 10) . ' poin)';
        }
        if ($beratKardus > 0) {
            $details[] = "Kardus: {$beratKardus} kg (" . ($beratKardus * 12) . ' poin)';
        }

        $message = 'Pengambilan sampah telah diselesaikan oleh ' . Auth::user()->name . ' untuk user ' . $wasteCollection->user->name . '. ' .
                   'Detail: ' . implode(', ', $details) . '. ' .
                   "Total poin: {$totalPoin} poin. Memerlukan approval.";

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => Notification::TYPE_POINT_TRANSACTION,
                'title' => 'Approval Poin Diperlukan',
                'message' => $message,
                'data' => [
                    'waste_collection_id' => $wasteCollection->id,
                    'user_id' => $wasteCollection->user_id,
                    'total_points' => $totalPoin,
                    'berat_kertas' => $beratKertas,
                    'berat_plastik' => $beratPlastik,
                    'berat_kardus' => $beratKardus,
                    'processed_by' => Auth::id(),
                    'action_needed' => 'approve_points'
                ],
                'notifiable_type' => 'App\Models\WasteCollection',
                'notifiable_id' => $wasteCollection->id,
            ]);
        }
    }

    private function sendUserNotification($wasteCollection, $totalPoin)
    {
        Notification::create([
            'user_id' => $wasteCollection->user_id,
            'type' => Notification::TYPE_COLLECTION_REQUEST,
            'title' => 'Sampah Berhasil Diambil',
            'message' => 'Sampah Anda telah berhasil diambil oleh petugas. ' .
                        "Total {$totalPoin} poin sedang menunggu approval admin.",
            'data' => [
                'waste_collection_id' => $wasteCollection->id,
                'total_points' => $totalPoin,
                'status' => 'completed_pending_approval',
            ],
            'notifiable_type' => 'App\Models\WasteCollection',
            'notifiable_id' => $wasteCollection->id,
        ]);
    }
}
