<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bin;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use App\Models\PointRedemptions;
use App\Models\PointTransactions;
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

    public function reports(Request $request)
    {
        // Validate input parameters
        $request->validate([
            'report_type' => 'required|in:transactions,redemptions,combined',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'nullable|string',
            'format' => 'nullable|in:pdf,view',
        ]);

        // Set default date range (last 30 days if not provided)
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        $reportType = $request->report_type ?? 'combined';
        $format = 'pdf';

        // Build queries based on report type
        $data = $this->buildReportData($request, $startDate, $endDate, $reportType);

        // Generate filename
        $filename = $this->generateFilename($reportType, $startDate, $endDate);

        if ($format === 'view') {
            return view('admin.reports.preview', compact('data', 'startDate', 'endDate', 'reportType'));
        }

        // Generate and download PDF using DomPDF
        return $this->generatePdfReport($data, $startDate, $endDate, $reportType, $filename);
    }

    private function buildReportData(Request $request, Carbon $startDate, Carbon $endDate, string $reportType): array
    {
        $data = [
            'transactions' => collect(),
            'redemptions' => collect(),
            'summary' => [
                'total_transactions' => 0,
                'total_redemptions' => 0,
                'total_points_earned' => 0,
                'total_points_redeemed' => 0,
                'total_cash_redeemed' => 0,
                'active_users' => 0,
                'pending_transactions' => 0,
                'completed_transactions' => 0,
            ],
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
                'days' => $startDate->diffInDays($endDate) + 1,
            ],
        ];

        // Build transactions query
        if (in_array($reportType, ['transactions', 'combined'])) {
            $transactionsQuery = PointTransactions::with(['user:id,name', 'processedBy:id,name'])->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

            if ($request->user_id) {
                $transactionsQuery->where('user_id', $request->user_id);
            }

            if ($request->status && $reportType === 'transactions') {
                $transactionsQuery->where('status', $request->status);
            }

            $data['transactions'] = $transactionsQuery->orderBy('created_at', 'desc')->get();

            // Calculate transaction summaries
            $data['summary']['total_transactions'] = $data['transactions']->count();
            $data['summary']['total_points_earned'] = $data['transactions']->where('transaction_type', 'deposit')->sum('points');
            $data['summary']['pending_transactions'] = $data['transactions']->where('status', PointTransactions::STATUS_PENDING)->count();
            $data['summary']['completed_transactions'] = $data['transactions']->where('status', PointTransactions::STATUS_APPROVED)->count();
        }

        // Build redemptions query
        if (in_array($reportType, ['redemptions', 'combined'])) {
            $redemptionsQuery = PointRedemptions::with(['user:id,name', 'processedBy:id,name'])->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

            if ($request->user_id) {
                $redemptionsQuery->where('user_id', $request->user_id);
            }

            if ($request->status && $reportType === 'redemptions') {
                $redemptionsQuery->where('status', $request->status);
            }

            $data['redemptions'] = $redemptionsQuery->orderBy('created_at', 'desc')->get();

            // Calculate redemption summaries
            $data['summary']['total_redemptions'] = $data['redemptions']->count();
            $data['summary']['total_points_redeemed'] = $data['redemptions']->sum('points_redeemed');
            $data['summary']['total_cash_redeemed'] = $data['redemptions']->sum('cash_value');
        }

        // Calculate active users
        $userIds = collect();
        if ($data['transactions']->isNotEmpty()) {
            $userIds = $userIds->merge($data['transactions']->pluck('user_id'));
        }
        if ($data['redemptions']->isNotEmpty()) {
            $userIds = $userIds->merge($data['redemptions']->pluck('user_id'));
        }
        $data['summary']['active_users'] = $userIds->unique()->count();

        return $data;
    }

    private function generateFilename(string $reportType, Carbon $startDate, Carbon $endDate): string
    {
        $typeMap = [
            'transactions' => 'Transaksi-Poin',
            'redemptions' => 'Penukaran-Poin',
            'combined' => 'Laporan-Lengkap',
        ];

        return sprintf('E-TRANK_%s_%s_sd_%s.pdf', $typeMap[$reportType], $startDate->format('d-m-Y'), $endDate->format('d-m-Y'));
    }

    private function generatePdfReport(array $data, Carbon $startDate, Carbon $endDate, string $reportType, string $filename)
    {
        // Additional report metadata
        $reportData = array_merge($data, [
            'report_type' => $reportType,
            'generated_at' => Carbon::now(),
            'generated_by' => auth()->user(),
            'report_title' => $this->getReportTitle($reportType),
            'company_info' => [
                'name' => 'E-TRANK System',
                'subtitle' => 'Sistem Monitoring Sampah Digital',
                'address' => 'Bandung, Jawa Barat, Indonesia',
                'phone' => '(022) 1234-5678',
                'email' => 'admin@etrank.system',
            ],
        ]);

        // Configure DomPDF options
        $pdf = Pdf::loadView('admin.reports.pdf-template', $reportData)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
            ]);

        return $pdf->download($filename);
    }

    private function getReportTitle(string $reportType): string
    {
        return match ($reportType) {
            'transactions' => 'Laporan Transaksi Poin',
            'redemptions' => 'Laporan Penukaran Poin',
            'combined' => 'Laporan Lengkap Sistem E-TRANK',
            default => 'Laporan Sistem E-TRANK',
        };
    }

    public function reportsIndex()
    {
        $users = User::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.index', compact('users'));
    }

    public function showReport() {}

    public function approveReport() {}

    public function rejectReport() {}

    public function statistics() {}

    public function assignReport() {}

    public function exportReports() {}

    public function exportUsers() {}
}