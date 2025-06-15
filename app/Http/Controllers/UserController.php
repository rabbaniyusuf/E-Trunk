<?php

namespace App\Http\Controllers;

use App\Models\NabungSampah;
use App\Models\Notification;
use App\Models\WasteBinType;
use Illuminate\Http\Request;
use App\Models\SensorReadings;
use App\Models\PointRedemptions;
use App\Models\PointTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Get user's waste bin
        $userBin = $user->wasteBin;

        // Initialize default values
        $data = [
            'balance' => $user->balance ?? 0,
            'recyclePercentage' => 0,
            'nonRecyclePercentage' => 0,
            'totalWasteVolume' => 0,
            'monthlyGrowth' => 0,
            'recentTransactions' => collect(),
            'availablePoints' => 0,
            'totalEarned' => 0,
            'totalRedeemed' => 0,
        ];

        if ($userBin) {
            // Get waste bin types (recycle and non-recycle)
            $recycleBin = $userBin->getRecycleBin();
            $nonRecycleBin = $userBin->getNonRecycleBin();

            // Get current percentages
            $data['recyclePercentage'] = $recycleBin ? $recycleBin->current_percentage : 0;
            $data['nonRecyclePercentage'] = $nonRecycleBin ? $nonRecycleBin->current_percentage : 0;

            // Calculate total volume based on current height and bin capacity
            if ($recycleBin && $nonRecycleBin) {
                $recycleVolume = ($recycleBin->current_height_cm / $recycleBin->max_height_cm) * ($userBin->capacity_liters / 2);
                $nonRecycleVolume = ($nonRecycleBin->current_height_cm / $nonRecycleBin->max_height_cm) * ($userBin->capacity_liters / 2);
                $data['totalWasteVolume'] = $recycleVolume + $nonRecycleVolume;
            }

            // Get monthly growth (compare current month vs last month)
            $currentMonth = now()->startOfMonth();
            $lastMonth = now()->subMonth()->startOfMonth();

            $currentMonthEarned = PointTransactions::where('user_id', $user->id)->where('transaction_type', 'deposit')->where('status', 'approved')->where('created_at', '>=', $currentMonth)->sum('points');

            $lastMonthEarned = PointTransactions::where('user_id', $user->id)
                ->where('transaction_type', 'deposit')
                ->where('status', 'approved')
                ->whereBetween('created_at', [$lastMonth, $currentMonth])
                ->sum('points');

            $data['monthlyGrowth'] = $lastMonthEarned > 0 ? round((($currentMonthEarned - $lastMonthEarned) / $lastMonthEarned) * 100, 1) : ($currentMonthEarned > 0 ? 100 : 0);
        }

        // Get point statistics
        $data['totalEarned'] = PointTransactions::where('user_id', $user->id)->where('transaction_type', 'deposit')->where('status', 'approved')->sum('points');

        $data['totalRedeemed'] = PointRedemptions::where('user_id', $user->id)->where('status', 'completed')->sum('points_redeemed');

        $data['availablePoints'] = $data['totalEarned'] - $data['totalRedeemed'];

        // Get recent transactions (last 5)
        $data['recentTransactions'] = PointTransactions::where('user_id', $user->id)
            ->with(['wasteBinType.bin'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent sensor readings for chart data
        $data['sensorData'] = [];
        if ($userBin) {
            $wasteBinTypes = $userBin->wasteBinTypes;
            foreach ($wasteBinTypes as $binType) {
                $readings = SensorReadings::where('waste_bin_type_id', $binType->id)
                    ->where('reading_time', '>=', now()->subDays(7))
                    ->orderBy('reading_time', 'asc')
                    ->get(['reading_time', 'percentage']);

                $data['sensorData'][$binType->type] = $readings->map(function ($reading) {
                    return [
                        'time' => $reading->reading_time->format('Y-m-d H:i'),
                        'percentage' => $reading->percentage,
                    ];
                });
            }
        }

        return view('user.dashboard.index', $data);
    }

    public function nabung()
    {
        $user = Auth::user();
        $wasteBin = $user->wasteBin;

        if (!$wasteBin) {
            return redirect()->route('user.dashboard')->with('error', 'Anda belum memiliki tempat sampah yang terdaftar. Silakan hubungi admin.');
        }

        $recycleBin = $wasteBin->getRecycleBin();
        $nonRecycleBin = $wasteBin->getNonRecycleBin();

        // Check if there's any percentage to deposit
        $availableRecyclePercentage = $recycleBin ? $recycleBin->current_percentage : 0;
        $availableNonRecyclePercentage = $nonRecycleBin ? $nonRecycleBin->current_percentage : 0;

        return view('user.nabung.create', compact('recycleBin', 'nonRecycleBin', 'availableRecyclePercentage', 'availableNonRecyclePercentage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'waste_bin_type_id' => 'required|exists:waste_bin_types,id',
            'percentage_to_deposit' => 'required|numeric|min:1|max:100',
        ]);

        $user = Auth::user();
        $wasteBinType = WasteBinType::findOrFail($request->waste_bin_type_id);

        // Verify the waste bin belongs to the user
        if ($wasteBinType->bin->bin_code !== $user->waste_bin_code) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke tempat sampah ini.');
        }

        // Check if the requested percentage is available
        if ($request->percentage_to_deposit > $wasteBinType->current_percentage) {
            return redirect()->back()->with('error', 'Persentase yang diminta melebihi sampah yang tersedia.');
        }

        DB::beginTransaction();

        try {
            // Calculate points (1% = 1 point)
            $points = (int) $request->percentage_to_deposit;

            // Create point transaction record
            $transaction = PointTransactions::create([
                'user_id' => $user->id,
                'waste_bin_type_id' => $wasteBinType->id,
                'transaction_type' => 'deposit',
                'points' => $points,
                'percentage_deposited' => $request->percentage_to_deposit,
                'description' => "Deposit sampah {$wasteBinType->type} sebesar {$request->percentage_to_deposit}%",
                'status' => 'pending',
            ]);

            // Create notification for admin to schedule waste collection
            $adminUsers = \App\Models\User::role('petugas_pusat')->get();
            foreach ($adminUsers as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'Permintaan Pengambilan Sampah',
                    'message' => "User {$user->name} telah menabung poin dari sampah {$wasteBinType->type}. Perlu dijadwalkan pengambilan sampah.",
                    'type' => 'waste_collection_request',
                    'data' => json_encode([
                        'user_id' => $user->id,
                        'transaction_id' => $transaction->id,
                        'waste_bin_type_id' => $wasteBinType->id,
                        'percentage' => $request->percentage_to_deposit,
                    ]),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('user.dashboard')
                ->with('success', "Berhasil mengajukan penukaran {$request->percentage_to_deposit}% sampah menjadi {$points} poin. Menunggu konfirmasi admin.");
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses permintaan.');
        }
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
