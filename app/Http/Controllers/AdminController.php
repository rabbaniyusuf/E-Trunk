<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard.index');
    }

    public function index(Request $request)
    {
        // Build query with filters
        $query = User::with(['bins', 'roles'])->whereHas('roles', function ($q) {
            $q->whereIn('name', ['masyarakat', 'petugas_kebersihan']);
        });

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('bins', function ($binQuery) use ($search) {
                        $binQuery->where('bin_code', 'like', "%{$search}%");
                    });
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // District filter
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        // Bin status filter
        if ($request->filled('bin_status')) {
            switch ($request->bin_status) {
                case 'active':
                    $query->whereHas('bins', function ($q) {
                        $q->where('status', 'active');
                    });
                    break;
                case 'inactive':
                    $query->whereHas('bins', function ($q) {
                        $q->where('status', 'inactive');
                    });
                    break;
                case 'full':
                    $query->whereHas('bins', function ($q) {
                        $q->whereRaw('current_weight >= capacity * 0.8');
                    });
                    break;
            }
        }

        // Get paginated results
        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get statistics
        $totalUsers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['masyarakat', 'petugas_kebersihan']);
        })->count();

        $activeBins = Bin::where('status', 'active')->count();

        $fullBins = Bin::whereRaw('current_weight >= capacity * 0.8')->count();

        $todayPickups = Bin::whereDate('last_pickup', Carbon::today())->count();

        // Get unique districts for filter dropdown
        $districts = User::whereNotNull('district')->where('district', '!=', '')->distinct()->pluck('district')->sort()->values();

        return view('admin.users.index', compact('users', 'totalUsers', 'activeBins', 'fullBins', 'todayPickups', 'districts'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'address' => 'required|string',
            'district' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
        ]);

        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'district' => $request->district,
                'postal_code' => $request->postal_code,
                'password' => Hash::make('password123'), // Default password
            ]);

            // Assign user role
            $user->assignRole('masyarakat');

            // Create bins for the user
            $this->createBinsForUser($user);

            return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan dengan 2 tong sampah');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function createBinsForUser(User $user)
    {
        $binTypes = ['recycle', 'non_recycle'];

        foreach ($binTypes as $type) {
            Bin::create([
                'user_id' => $user->id,
                'bin_code' => Bin::generateBinCode($user->id, $type),
                'type' => $type,
                'status' => 'active',
                'capacity' => 100, // 100 kg default capacity
            ]);
        }
    }

    public function toggleStatus() {}

    public function reports() {}

    public function showReport() {}

    public function approveReport() {}

    public function rejectReport() {}

    public function statistics() {}

    public function assignReport() {}

    public function exportReports() {}

    public function exportUsers() {}
}
