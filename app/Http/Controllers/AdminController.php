<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bin;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get recent notifications for sidebar/quick view
        $recentNotifications = Notification::where('user_id', Auth::id())->orderBy('created_at', 'desc')->take(5)->get();

        $unreadNotificationCount = Notification::where('user_id', Auth::id())->unread()->count();

        // Sample statistics (replace with actual data from your models)
        $statistics = [
            'total_users' => 150,
            'total_transactions' => 45,
            'daily_waste' => '1.2kg',
            'active_officers' => 8,
            'total_balance' => 2500000,
            'recyclable_waste' => 1247,
            'non_recyclable_waste' => 328,
        ];

        return view('admin.dashboard.index', compact('recentNotifications', 'unreadNotificationCount', 'statistics'));
    }

    public function index(Request $request)
    {
        // Build query with filters
        $users = User::role('masyarakat')->with('wasteBin')->latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $availableBins = Bin::active()->whereDoesntHave('users')->get();

        return view('admin.users.create', compact('availableBins'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'district' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'waste_bin_code' => [
                'required',
                'string',
                Rule::exists('bins', 'bin_code')->where(function ($query) {
                    $query->where('is_active', true);
                }),
                'unique:users,waste_bin_code',
            ],
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'district' => $request->district,
                'postal_code' => $request->postal_code,
                'waste_bin_code' => $request->waste_bin_code,
                'password' => Hash::make($request->password),
                'balance' => 0,
            ]);

            // Assign role masyarakat
            $user->assignRole('masyarakat');

            return redirect()->route('admin.users.index')->with('success', 'Akun user berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat membuat akun: ' . $e->getMessage())
                ->withInput();
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
