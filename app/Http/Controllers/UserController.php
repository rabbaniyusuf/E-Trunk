<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bin;
use App\Models\User;
use App\Models\Schedules;
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

        // Get waste bin data from waste_bin_type table
        $wasteBinData = $this->getWasteBinDataFromType();

        // Get recent transactions (last 5)
        $recentTransactions = PointTransactions::with(['wasteBinType', 'user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent waste collections (last 5)
        $recentWasteCollections = WasteCollection::with(['wasteBinType', 'assignedTo'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent redemptions (last 3)
        $recentRedemptions = PointRedemptions::with(['user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Combine all activities and sort by created_at
        $recentActivities = $this->combineRecentActivities($recentTransactions, $recentWasteCollections, $recentRedemptions);

        return view('user.dashboard.index', array_merge(compact('availablePoints', 'monthlyGrowth', 'recentActivities'), $wasteBinData));
    }

    /**
     * Combine and sort all recent activities
     */
    private function combineRecentActivities($transactions, $collections, $redemptions)
    {
        $activities = collect();

        // Add transactions
        foreach ($transactions as $transaction) {
            $activities->push([
                'type' => 'transaction',
                'data' => $transaction,
                'created_at' => $transaction->created_at,
                'title' => $transaction->getTypeLabel(),
                'subtitle' => $transaction->wasteBinType ? ucfirst($transaction->wasteBinType->type) : '',
                'description' => $transaction->description,
                'points' => ($transaction->transaction_type == 'deposit' ? '+' : '-') . number_format($transaction->points),
                'status' => $transaction->getStatusLabel(),
                'status_class' => $this->getStatusColorClass($transaction->status),
                'icon' => $transaction->transaction_type == 'deposit' ? 'bi-plus-circle' : 'bi-dash-circle',
                'icon_class' => $transaction->getTypeColor(),
            ]);
        }

        // Add waste collections
        foreach ($collections as $collection) {
            $activities->push([
                'type' => 'waste_collection',
                'data' => $collection,
                'created_at' => $collection->created_at,
                'title' => 'Pengambilan Sampah',
                'subtitle' => $collection->getWasteTypesLabel(),
                'description' => $collection->notes,
                'points' => null,
                'status' => $collection->getStatusLabel(),
                'status_class' => $this->getWasteCollectionStatusClass($collection->status),
                'icon' => $this->getWasteCollectionIcon($collection->status),
                'icon_class' => $this->getWasteCollectionIconClass($collection->status),
                'pickup_info' => $collection->pickup_date ? Carbon::parse($collection->pickup_date)->format('d M Y') . ' • ' . $collection->pickup_time : null,
                'assigned_petugas' => $collection->assignedTo ? $collection->assignedTo->name : null,
            ]);
        }

        // Add redemptions
        foreach ($redemptions as $redemption) {
            $activities->push([
                'type' => 'redemption',
                'data' => $redemption,
                'created_at' => $redemption->created_at,
                'title' => 'Penukaran Poin',
                'subtitle' => ucfirst($redemption->redemption_type),
                'description' => $redemption->notes,
                'points' => '-' . number_format($redemption->points_redeemed),
                'status' => ucfirst($redemption->status),
                'status_class' => $this->getRedemptionStatusClass($redemption->status),
                'icon' => 'bi-gift',
                'icon_class' => 'warning',
                'cash_value' => $redemption->cash_value ? 'Rp ' . number_format($redemption->cash_value, 0, ',', '.') : null,
            ]);
        }

        // Sort by created_at descending and take only 10 latest
        return $activities->sortByDesc('created_at')->take(10)->values();
    }

    /**
     * Get status color class for transactions
     */
    private function getStatusColorClass($status)
    {
        return match ($status) {
            PointTransactions::STATUS_PENDING => 'status-pending',
            PointTransactions::STATUS_APPROVED => 'status-approved',
            PointTransactions::STATUS_REJECTED => 'status-rejected',
            default => 'status-pending',
        };
    }

    /**
     * Get status color class for waste collections
     */
    private function getWasteCollectionStatusClass($status)
    {
        return match ($status) {
            WasteCollection::STATUS_PENDING => 'status-pending',
            WasteCollection::STATUS_SCHEDULED => 'status-processing',
            WasteCollection::STATUS_IN_PROGRESS => 'status-processing',
            WasteCollection::STATUS_COMPLETED => 'status-approved',
            WasteCollection::STATUS_CANCELLED => 'status-rejected',
            default => 'status-pending',
        };
    }

    /**
     * Get icon for waste collections
     */
    private function getWasteCollectionIcon($status)
    {
        return match ($status) {
            WasteCollection::STATUS_PENDING => 'bi-clock',
            WasteCollection::STATUS_SCHEDULED => 'bi-calendar-check',
            WasteCollection::STATUS_IN_PROGRESS => 'bi-truck',
            WasteCollection::STATUS_COMPLETED => 'bi-check-circle',
            WasteCollection::STATUS_CANCELLED => 'bi-x-circle',
            default => 'bi-recycle',
        };
    }

    /**
     * Get icon class for waste collections
     */
    private function getWasteCollectionIconClass($status)
    {
        return match ($status) {
            WasteCollection::STATUS_PENDING => 'warning',
            WasteCollection::STATUS_SCHEDULED => 'secondary',
            WasteCollection::STATUS_IN_PROGRESS => 'primary',
            WasteCollection::STATUS_COMPLETED => 'success',
            WasteCollection::STATUS_CANCELLED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status color class for redemptions
     */
    private function getRedemptionStatusClass($status)
    {
        return match ($status) {
            'pending' => 'status-pending',
            'approved', 'completed' => 'status-approved',
            'rejected', 'cancelled' => 'status-rejected',
            default => 'status-pending',
        };
    }

    private function calculateAvailablePoints(User $user): int
    {
        // Calculate total points from approved deposits
        $totalDeposits = PointTransactions::where('user_id', $user->id)->where('transaction_type', 'deposit')->where('status', PointTransactions::STATUS_APPROVED)->sum('points');

        // Calculate total points from approved withdrawals
        $totalWithdrawals = PointTransactions::where('user_id', $user->id)->where('transaction_type', 'withdrawal')->where('status', PointTransactions::STATUS_APPROVED)->sum('points');

        // Calculate total redeemed points
        $totalRedemptions = PointRedemptions::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'approved'])
            ->sum('points_redeemed');

        return max(0, $totalDeposits - $totalWithdrawals - $totalRedemptions);
    }

    private function calculateMonthlyGrowth(User $user): float
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Current month points
        $currentMonthPoints = PointTransactions::where('user_id', $user->id)->where('transaction_type', 'deposit')->where('status', PointTransactions::STATUS_APPROVED)->where('created_at', '>=', $currentMonth)->sum('points');

        // Previous month points
        $previousMonthPoints = PointTransactions::where('user_id', $user->id)
            ->where('transaction_type', 'deposit')
            ->where('status', PointTransactions::STATUS_APPROVED)
            ->whereBetween('created_at', [$previousMonth, $previousMonthEnd])
            ->sum('points');

        if ($previousMonthPoints == 0) {
            return $currentMonthPoints > 0 ? 100 : 0;
        }

        return round((($currentMonthPoints - $previousMonthPoints) / $previousMonthPoints) * 100, 1);
    }

    private function getWasteBinDataFromType(): array
    {
        $user_bin_id = Auth::user()->waste_bin_code ? Bin::where('bin_code', Auth::user()->waste_bin_code)->value('id') : null;

        // Get waste bin types with percentage data
        $recycleBin = WasteBinType::where('type', 'recycle')->where('bin_id', $user_bin_id)->first();
        $nonRecycleBin = WasteBinType::where('type', 'non_recycle')->where('bin_id', $user_bin_id)->first();

        $recyclePercentage = (int) ($recycleBin->current_percentage ?? 0);
        $nonRecyclePercentage = (int) ($nonRecycleBin->current_percentage ?? 0);

        // Calculate total waste volume (assuming each bin has 50L capacity)
        $binCapacity = 50; // Liters per bin
        $recycleVolume = ($recyclePercentage / 100) * $binCapacity;
        $nonRecycleVolume = ($nonRecyclePercentage / 100) * $binCapacity;
        $totalWasteVolume = $recycleVolume + $nonRecycleVolume;

        return [
            'recyclePercentage' => $recyclePercentage,
            'nonRecyclePercentage' => $nonRecyclePercentage,
            'totalWasteVolume' => $totalWasteVolume,
            'recycleVolume' => $recycleVolume,
            'nonRecycleVolume' => $nonRecycleVolume,
        ];
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

            // Cek apakah user sudah memiliki permintaan yang masih active
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
            $wasteTypes = collect($request->waste_types)->map(fn($type) => ucfirst($type))->join(', ');

            $collectionRequest = WasteCollection::create([
                'user_id' => $user->id,
                'waste_bin_type_id' => $wasteBinType->id,
                'waste_types' => $request->waste_types,
                'pickup_date' => $request->pickup_date,
                'pickup_time' => $request->pickup_time,
                'status' => WasteCollection::STATUS_PENDING,
                'notes' => 'Permintaan pengambilan sampah: ' . $wasteTypes,
            ]);

            // Buat jadwal untuk petugas (otomatis assign ke petugas yang tersedia)
            $availablePetugas = $this->getAvailablePetugas($request->pickup_date, $request->pickup_time);

            $schedule = Schedules::create([
                'petugas_id' => $availablePetugas?->id, // Bisa null jika belum ada petugas tersedia
                'user_id' => $user->id,
                'bin_id' => $user->wasteBin->id,
                'schedule_type' => 'waste_collection',
                'scheduled_date' => $request->pickup_date,
                'scheduled_time' => $this->parseTimeSlot($request->pickup_time),
                'priority' => 'medium',
                'status' => $availablePetugas ? 'scheduled' : 'pending',
                'notes' => 'Jadwal pengambilan sampah - ' . $wasteTypes,
            ]);

            // Update status collection request jika sudah ada petugas
            if ($availablePetugas) {
                $collectionRequest->update([
                    'status' => WasteCollection::STATUS_SCHEDULED,
                    'assigned_to' => $availablePetugas->id,
                ]);
            }

            // Kirim notifikasi
            $this->sendNotifications($collectionRequest, $schedule, $availablePetugas);

            DB::commit();

            Log::info('Waste collection request created successfully', [
                'user_id' => $user->id,
                'collection_request_id' => $collectionRequest->id,
                'schedule_id' => $schedule->id,
                'petugas_assigned' => $availablePetugas?->id,
                'waste_types' => $request->waste_types,
                'pickup_schedule' => $request->pickup_date . ' ' . $request->pickup_time,
            ]);

            $successMessage = $availablePetugas ? 'Permintaan pengambilan sampah berhasil diajukan! Petugas ' . $availablePetugas->name . ' akan menghubungi Anda untuk konfirmasi jadwal pada ' . Carbon::parse($request->pickup_date)->format('d M Y') . ' pukul ' . $request->pickup_time . '.' : 'Permintaan pengambilan sampah berhasil diajukan! Admin akan menugaskan petugas dan menghubungi Anda untuk konfirmasi jadwal pada ' . Carbon::parse($request->pickup_date)->format('d M Y') . ' pukul ' . $request->pickup_time . '.';

            return redirect()->route('user.nabung')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create waste collection request', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage() ?: 'Terjadi kesalahan saat mengajukan permintaan pengambilan sampah. Silakan coba lagi.');
        }
    }

    /**
     * Mendapatkan petugas yang tersedia pada jadwal tertentu
     */
    private function getAvailablePetugas($date, $timeSlot)
    {
        // Ambil semua petugas sampah (role petugas_kebersihan)
        $petugasList = User::all()->filter(function ($user) {
            return $user->hasRole('petugas_kebersihan');
        });

        if ($petugasList->isEmpty()) {
            return null;
        }

        // Cari petugas yang tidak memiliki jadwal pada waktu yang sama
        foreach ($petugasList as $petugas) {
            $hasConflict = Schedules::where('petugas_id', $petugas->id)
                ->where('scheduled_date', $date)
                ->where('scheduled_time', $this->parseTimeSlot($timeSlot))
                ->whereIn('status', ['scheduled', 'in_progress'])
                ->exists();

            if (!$hasConflict) {
                return $petugas;
            }
        }

        // Jika semua petugas sibuk, return petugas dengan beban kerja paling sedikit
        return $petugasList
            ->sortBy(function ($petugas) use ($date) {
                return Schedules::where('petugas_id', $petugas->id)
                    ->where('scheduled_date', $date)
                    ->whereIn('status', ['scheduled', 'in_progress'])
                    ->count();
            })
            ->first();
    }

    /**
     * Parse time slot menjadi time format
     */
    private function parseTimeSlot($timeSlot)
    {
        // Ambil jam mulai dari time slot (contoh: "08:00-10:00" -> "08:00")
        return explode('-', $timeSlot)[0];
    }

    /**
     * Kirim notifikasi ke user dan petugas
     */
    private function sendNotifications($collectionRequest, $schedule, $petugas = null)
    {
        $user = $collectionRequest->user;
        $pickupDate = Carbon::parse($collectionRequest->pickup_date)->format('d M Y');

        // Notifikasi untuk user
        Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_COLLECTION_REQUEST,
            'title' => 'Permintaan Pengambilan Sampah Diterima',
            'message' => $petugas ? "Permintaan Anda telah dijadwalkan pada {$pickupDate} pukul {$collectionRequest->pickup_time}. Petugas {$petugas->name} akan menghubungi Anda." : 'Permintaan Anda sedang diproses. Admin akan menugaskan petugas dan menghubungi Anda segera.',
            'data' => [
                'collection_request_id' => $collectionRequest->id,
                'schedule_id' => $schedule->id,
                'pickup_date' => $collectionRequest->pickup_date,
                'pickup_time' => $collectionRequest->pickup_time,
            ],
            'notifiable_type' => WasteCollection::class,
            'notifiable_id' => $collectionRequest->id,
        ]);

        // Notifikasi untuk petugas jika sudah ditugaskan
        if ($petugas) {
            Notification::create([
                'user_id' => $petugas->id,
                'type' => Notification::TYPE_SCHEDULE_UPDATE,
                'title' => 'Jadwal Pengambilan Sampah Baru',
                'message' => "Anda ditugaskan untuk mengambil sampah dari {$user->name} pada {$pickupDate} pukul {$collectionRequest->pickup_time}.",
                'data' => [
                    'collection_request_id' => $collectionRequest->id,
                    'schedule_id' => $schedule->id,
                    'user_name' => $user->name,
                    'user_address' => $user->address ?? 'Alamat tidak tersedia',
                    'waste_types' => $collectionRequest->waste_types,
                ],
                'notifiable_type' => Schedules::class,
                'notifiable_id' => $schedule->id,
            ]);
        }

        // Notifikasi untuk semua admin jika belum ada petugas
        if (!$petugas) {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => Notification::TYPE_COLLECTION_REQUEST,
                    'title' => 'Permintaan Pengambilan Sampah Baru',
                    'message' => "Permintaan baru dari {$user->name} untuk tanggal {$pickupDate}. Perlu penugasan petugas.",
                    'data' => [
                        'collection_request_id' => $collectionRequest->id,
                        'schedule_id' => $schedule->id,
                        'user_name' => $user->name,
                        'pickup_date' => $collectionRequest->pickup_date,
                        'pickup_time' => $collectionRequest->pickup_time,
                    ],
                    'notifiable_type' => WasteCollection::class,
                    'notifiable_id' => $collectionRequest->id,
                ]);
            }
        }
    }

    /**
     * Kirim notifikasi ke semua petugas sampah
     */
    // private function notifyPetugasSampah(WasteCollection $collectionRequest)
    // {
    //     $petugasSampah = User::role('petugas_kebersihan')->get();

    //     foreach ($petugasSampah as $petugas) {
    //         Notification::create([
    //             'user_id' => $petugas->id,
    //             'type' => Notification::TYPE_COLLECTION_REQUEST,
    //             'title' => 'Permintaan Pengambilan Sampah Baru',
    //             'message' => "Permintaan pengambilan sampah dari {$collectionRequest->user->name} untuk tanggal {$collectionRequest->pickup_date->format('d M Y')} pukul {$collectionRequest->pickup_time}.",
    //             'data' => [
    //                 'collection_request_id' => $collectionRequest->id,
    //                 'user_name' => $collectionRequest->user->name,
    //                 'user_address' => $collectionRequest->user->address,
    //                 'pickup_date' => $collectionRequest->pickup_date->format('Y-m-d'),
    //                 'pickup_time' => $collectionRequest->pickup_time,
    //                 'waste_types' => $collectionRequest->getWasteTypesLabel(),
    //                 // 'action_url' => route('petugas.collection-requests.show', $collectionRequest->id),
    //             ],
    //             'notifiable_type' => WasteCollection::class,
    //             'notifiable_id' => $collectionRequest->id,
    //         ]);
    //     }
    // }

    // /**
    //  * Kirim notifikasi konfirmasi ke user
    //  */
    // private function notifyUser(User $user, WasteCollection $collectionRequest)
    // {
    //     Notification::create([
    //         'user_id' => $user->id,
    //         'type' => Notification::TYPE_COLLECTION_REQUEST,
    //         'title' => 'Permintaan Pengambilan Sampah Berhasil Diajukan',
    //         'message' => "Permintaan pengambilan sampah Anda untuk tanggal {$collectionRequest->pickup_date->format('d M Y')} pukul {$collectionRequest->pickup_time} telah berhasil diajukan. Petugas akan segera memproses permintaan Anda.",
    //         'data' => [
    //             'collection_request_id' => $collectionRequest->id,
    //             'pickup_date' => $collectionRequest->pickup_date->format('Y-m-d'),
    //             'pickup_time' => $collectionRequest->pickup_time,
    //             'waste_types' => $collectionRequest->getWasteTypesLabel(),
    //             'estimated_points' => floor($collectionRequest->wasteBinType->current_percentage),
    //             // 'action_url' => route('user.nabung.show', $collectionRequest->id),
    //         ],
    //         'notifiable_type' => WasteCollection::class,
    //         'notifiable_id' => $collectionRequest->id,
    //     ]);
    // }

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