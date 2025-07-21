<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bin;
use App\Models\User;
use App\Models\NabungSampah;
use App\Models\Notification;
use App\Models\WasteBinType;
use Illuminate\Http\Request;
use App\Models\SensorReadings;
use App\Models\WasteCollection;
use App\Models\PointRedemptions;
use App\Models\PointTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Get user's available points
        $availablePoints = $this->calculateAvailablePoints($user);

        // Get monthly growth percentage
        $monthlyGrowth = $this->calculateMonthlyGrowth($user);

        // Get sensor readings for waste bin percentages
        $wasteBinData = $this->getWasteBinData();

        // Get recent transactions (last 10)
        $recentTransactions = PointTransactions::with(['wasteBinType', 'user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent redemptions (last 5)
        $recentRedemptions = PointRedemptions::with(['user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('user.dashboard.index', compact(
            'availablePoints',
            'monthlyGrowth',
            'recentTransactions',
            'recentRedemptions',
            'wasteBinData'
        ) + $wasteBinData);
    }

     private function calculateAvailablePoints(User $user): int
    {
        // Calculate total points from approved deposits
        $totalDeposits = PointTransactions::where('user_id', $user->id)
            ->where('transaction_type', 'deposit')
            ->where('status', PointTransactions::STATUS_APPROVED)
            ->sum('points');

        // Calculate total points from approved withdrawals
        $totalWithdrawals = PointTransactions::where('user_id', $user->id)
            ->where('transaction_type', 'withdrawal')
            ->where('status', PointTransactions::STATUS_APPROVED)
            ->sum('points');

        // Calculate total redeemed points
        $totalRedemptions = PointRedemptions::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'approved'])
            ->sum('points_redeemed');

        return $totalDeposits - $totalWithdrawals - $totalRedemptions;
    }

    private function calculateMonthlyGrowth(User $user): float
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Current month points
        $currentMonthPoints = PointTransactions::where('user_id', $user->id)
            ->where('transaction_type', 'deposit')
            ->where('status', PointTransactions::STATUS_APPROVED)
            ->where('created_at', '>=', $currentMonth)
            ->sum('points');

        // Previous month points
        $previousMonthPoints = PointTransactions::where('user_id', $user->id)
            ->where('transaction_type', 'deposit')
            ->where('status', PointTransactions::STATUS_APPROVED)
            ->whereBetween('created_at', [$previousMonth, $previousMonthEnd])
            ->sum('points');

        if ($previousMonthPoints == 0) {
            return $currentMonthPoints > 0 ? 100 : 0;
        }

        return (($currentMonthPoints - $previousMonthPoints) / $previousMonthPoints) * 100;
    }

    private function getWasteBinData(): array
    {
        // Get latest sensor readings for each waste bin type
        $recycleData = SensorReadings::with('wasteBinType')
            ->whereHas('wasteBinType', function($query) {
                $query->where('type', 'recycle');
            })
            ->latest('reading_time')
            ->first();

        $nonRecycleData = SensorReadings::with('wasteBinType')
            ->whereHas('wasteBinType', function($query) {
                $query->where('type', 'non_recycle');
            })
            ->latest('reading_time')
            ->first();

        $recyclePercentage = $recycleData ? $recycleData->percentage : 0;
        $nonRecyclePercentage = $nonRecycleData ? $nonRecycleData->percentage : 0;

        // Calculate total volume (assuming each bin has 50L capacity)
        $totalWasteVolume = ($recyclePercentage + $nonRecyclePercentage) * 0.5;

        return [
            'recyclePercentage' => $recyclePercentage,
            'nonRecyclePercentage' => $nonRecyclePercentage,
            'totalWasteVolume' => $totalWasteVolume,
            'recycleData' => $recycleData,
            'nonRecycleData' => $nonRecycleData,
        ];
    }

    public function refreshSensorData()
    {
        // AJAX endpoint untuk refresh data sensor secara real-time
        $wasteBinData = $this->getWasteBinData();

        return response()->json($wasteBinData);
    }

    public function getRecentActivity(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);

        $transactions = PointTransactions::with(['wasteBinType', 'user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $redemptions = PointRedemptions::with(['user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'transactions' => $transactions,
            'redemptions' => $redemptions
        ]);
    }

    public function nabung()
    {
        $user = Auth::user();

        // Get user's waste bin through bin_code
        $wasteBin = null;
        if ($user->waste_bin_code) {
            $wasteBin = Bin::where('bin_code', $user->waste_bin_code)->first();
        }

        if (!$wasteBin) {
            return view('user.nabung.create', [
                'recycleBin' => null,
                'availableRecyclePercentage' => 0,
                'error' => 'Anda belum memiliki tempat sampah yang terdaftar. Silakan hubungi admin untuk registrasi kode tempat sampah.',
            ]);
        }

        $recycleBin = $wasteBin->getRecycleBin();
        $availableRecyclePercentage = $recycleBin ? $recycleBin->current_percentage : 0;

        return view('user.nabung.create', compact('recycleBin', 'availableRecyclePercentage'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make(
            $request->all(),
            [
                'waste_bin_type_id' => 'required|exists:waste_bin_types,id',
                'waste_types' => 'required|array|min:1',
                'waste_types.*' => 'required|in:kardus,plastik,kertas',
                'pickup_date' => 'required|date|after:today|before_or_equal:' . Carbon::now()->addDays(7)->toDateString(),
                'pickup_time' => 'required|in:08:00-10:00,10:00-12:00,13:00-15:00,15:00-17:00',
            ],
            [
                'waste_bin_type_id.required' => 'Tempat sampah tidak ditemukan.',
                'waste_bin_type_id.exists' => 'Tempat sampah tidak valid.',
                'waste_types.required' => 'Silakan pilih minimal satu jenis sampah.',
                'waste_types.min' => 'Silakan pilih minimal satu jenis sampah.',
                'waste_types.*.in' => 'Jenis sampah tidak valid.',
                'pickup_date.required' => 'Tanggal pengambilan harus diisi.',
                'pickup_date.after' => 'Tanggal pengambilan minimal besok.',
                'pickup_date.before_or_equal' => 'Tanggal pengambilan maksimal 7 hari ke depan.',
                'pickup_time.required' => 'Waktu pengambilan harus diisi.',
                'pickup_time.in' => 'Waktu pengambilan tidak valid.',
            ],
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Terjadi kesalahan pada data yang diisi.');
        }

        try {
            DB::beginTransaction();

            $user = auth()->user();

            // Cek apakah user memiliki tempat sampah dan ada sampah yang bisa ditukar
            $wasteBinType = $user->wasteBin?->wasteBinTypes()->where('id', $request->waste_bin_type_id)->where('type', 'recycle')->first();

            if (!$wasteBinType) {
                return redirect()->back()->with('error', 'Tempat sampah daur ulang tidak ditemukan atau tidak terdaftar.');
            }

            if ($wasteBinType->current_percentage <= 0) {
                return redirect()->back()->with('error', 'Tempat sampah daur ulang Anda masih kosong.');
            }

            // Cek apakah user sudah memiliki permintaan yang masih pending
            $existingRequest = WasteCollection::where('user_id', $user->id)
                ->whereIn('status', [WasteCollection::STATUS_PENDING, WasteCollection::STATUS_SCHEDULED, WasteCollection::STATUS_IN_PROGRESS])
                ->exists();

            if ($existingRequest) {
                return redirect()->back()->with('error', 'Anda masih memiliki permintaan pengambilan sampah yang belum selesai.');
            }

            // Cek ketersediaan jadwal untuk mencegah konflik
            $conflictingRequest = WasteCollection::where('pickup_date', $request->pickup_date)
                ->where('pickup_time', $request->pickup_time)
                ->whereIn('status', [WasteCollection::STATUS_SCHEDULED, WasteCollection::STATUS_IN_PROGRESS])
                ->exists();

            if ($conflictingRequest) {
                return redirect()->back()->with('error', 'Jadwal tersebut sudah diambil. Silakan pilih jadwal lain.');
            }

            // Buat permintaan pengambilan sampah
            $collectionRequest = WasteCollection::create([
                'user_id' => $user->id,
                'waste_bin_type_id' => $wasteBinType->id,
                'waste_types' => $request->waste_types,
                'pickup_date' => $request->pickup_date,
                'pickup_time' => $request->pickup_time,
                'status' => WasteCollection::STATUS_PENDING,
                'notes' => 'Permintaan pengambilan sampah: ' . collect($request->waste_types)->map(fn($type) => ucfirst($type))->join(', '),
            ]);

            // Buat transaksi poin dengan status pending
            $pointTransaction = PointTransactions::create([
                'user_id' => $user->id,
                'waste_bin_type_id' => $wasteBinType->id,
                'waste_collection_id' => $collectionRequest->id,
                'transaction_type' => 'deposit',
                'points' => floor($wasteBinType->current_percentage), // 1% = 1 poin
                'percentage_deposited' => $wasteBinType->current_percentage,
                'description' => 'Penukaran sampah daur ulang menjadi poin - ' . collect($request->waste_types)->map(fn($type) => ucfirst($type))->join(', '),
                'status' => PointTransactions::STATUS_PENDING,
            ]);

            // Kirim notifikasi ke semua petugas sampah
            $this->notifyPetugasSampah($collectionRequest);

            // Kirim notifikasi konfirmasi ke user
            $this->notifyUser($user, $collectionRequest);

            DB::commit();

            Log::info('Waste collection request created successfully', [
                'user_id' => $user->id,
                'collection_request_id' => $collectionRequest->id,
                'point_transaction_id' => $pointTransaction->id,
                'waste_types' => $request->waste_types,
                'pickup_schedule' => $request->pickup_date . ' ' . $request->pickup_time,
            ]);

            return redirect()
                ->route('user.nabung')
                ->with('success', 'Permintaan pengambilan sampah berhasil diajukan! Petugas akan menghubungi Anda untuk konfirmasi jadwal pengambilan pada ' . Carbon::parse($request->pickup_date)->format('d M Y') . ' pukul ' . $request->pickup_time . '.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create waste collection request', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with('error', $e->getMessage() ?: 'Terjadi kesalahan saat mengajukan permintaan pengambilan sampah. Silakan coba lagi.');
        }
    }

    /**
     * Kirim notifikasi ke semua petugas sampah
     */
    private function notifyPetugasSampah(WasteCollection $collectionRequest)
    {
        $petugasSampah = User::role('petugas_kebersihan')->get();

        foreach ($petugasSampah as $petugas) {
            Notification::create([
                'user_id' => $petugas->id,
                'type' => Notification::TYPE_COLLECTION_REQUEST,
                'title' => 'Permintaan Pengambilan Sampah Baru',
                'message' => "Permintaan pengambilan sampah dari {$collectionRequest->user->name} untuk tanggal {$collectionRequest->pickup_date->format('d M Y')} pukul {$collectionRequest->pickup_time}.",
                'data' => [
                    'collection_request_id' => $collectionRequest->id,
                    'user_name' => $collectionRequest->user->name,
                    'user_address' => $collectionRequest->user->address,
                    'pickup_date' => $collectionRequest->pickup_date->format('Y-m-d'),
                    'pickup_time' => $collectionRequest->pickup_time,
                    'waste_types' => $collectionRequest->getWasteTypesLabel(),
                    // 'action_url' => route('petugas.collection-requests.show', $collectionRequest->id),
                ],
                'notifiable_type' => WasteCollection::class,
                'notifiable_id' => $collectionRequest->id,
            ]);
        }
    }

    /**
     * Kirim notifikasi konfirmasi ke user
     */
    private function notifyUser(User $user, WasteCollection $collectionRequest)
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_COLLECTION_REQUEST,
            'title' => 'Permintaan Pengambilan Sampah Berhasil Diajukan',
            'message' => "Permintaan pengambilan sampah Anda untuk tanggal {$collectionRequest->pickup_date->format('d M Y')} pukul {$collectionRequest->pickup_time} telah berhasil diajukan. Petugas akan segera memproses permintaan Anda.",
            'data' => [
                'collection_request_id' => $collectionRequest->id,
                'pickup_date' => $collectionRequest->pickup_date->format('Y-m-d'),
                'pickup_time' => $collectionRequest->pickup_time,
                'waste_types' => $collectionRequest->getWasteTypesLabel(),
                'estimated_points' => floor($collectionRequest->wasteBinType->current_percentage),
                // 'action_url' => route('user.nabung.show', $collectionRequest->id),
            ],
            'notifiable_type' => WasteCollection::class,
            'notifiable_id' => $collectionRequest->id,
        ]);
    }

    public function tukarPoin()
    {
        $user = Auth::user();
        $currentPoints = $user->balance ?? 0;
        $validPointsOptions = PointRedemptions::getValidPointsOptions();

        // Filter options that user can afford
        $availableOptions = array_filter($validPointsOptions, function ($points) use ($currentPoints) {
            return $points <= $currentPoints;
        });

        return view('user.nabung.tukar-poin', compact('currentPoints', 'availableOptions'));
    }

    // public function calculatePoints(Request $request)
    // {
    //     $berat = $request->get('berat', 0);
    //     $jenis = $request->get('jenis', 'plastik');

    //     $hargaPerKg = [
    //         'plastik' => 2000,
    //         'kertas' => 1500,
    //         'logam' => 5000,
    //         'kaca' => 1000,
    //         'organik' => 500,
    //         'elektronik' => 3000,
    //     ];

    //     $totalHarga = $berat * ($hargaPerKg[$jenis] ?? 2000);
    //     $poin = NabungSampah::convertToPoints($totalHarga);

    //     return response()->json([
    //         'total_harga' => $totalHarga,
    //         'poin' => $poin,
    //         'total_harga_formatted' => 'Rp ' . number_format($totalHarga, 0, ',', '.'),
    //     ]);
    // }
}
