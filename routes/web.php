<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public Routes

// Guest Routes (hanya bisa diakses jika belum login)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::middleware('guest')->group(function () {
    // Authentication Routes
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Password Reset Routes (Optional)
    // Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    // Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    // Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    // Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated Routes (hanya bisa diakses jika sudah login)
Route::middleware('auth')->group(function () {
    // Dashboard - Redirect berdasarkan role
    // Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // // Profile Routes - Semua user bisa akses
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::put('/profile', [HomeController::class, 'updateProfile'])->name('profile.update');

    // ===========================================
    // ROLE-BASED ROUTES
    // ===========================================

    Route::prefix('notifications')
        ->name('notifications.')
        ->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::put('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('markAllRead');
            Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unreadCount');
        });

    // Routes untuk PETUGAS PUSAT (Admin)
    Route::middleware(['role:petugas_pusat'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Dashboard Admin
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::prefix('users')
                ->name('users.')
                ->group(function () {
                    Route::get('/', [AdminController::class, 'index'])->name('index');
                    Route::get('/create', [AdminController::class, 'create'])->name(name: 'create');
                    Route::post('/create', [AdminController::class, 'store'])->name(name: 'store');
                    Route::get('/{user}/edit', [AdminController::class, 'edit'])->name('edit');
                });

            Route::prefix('approvals')
                ->name('approvals.')
                ->group(function () {
                    Route::get('/', [ApprovalController::class, 'index'])->name('index');
                    Route::get('/{approval}/edit', [ApprovalController::class, 'edit'])->name('edit');
                    Route::put('/{approval}', [ApprovalController::class, 'update'])->name('update');
                    Route::post('/approval/bulk-update', [ApprovalController::class, 'bulkUpdate'])->name('bulk-update');
                });

            // User Management
            Route::resource('users', AdminController::class)->except(['show']);
            Route::get('/users/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('users.toggle-status');

            // Report Management
            Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');
            Route::get('/reports/{report}', [AdminController::class, 'showReport'])->name('reports.show');
            Route::put('/reports/{report}/approve', [AdminController::class, 'approveReport'])->name('reports.approve');
            Route::put('/reports/{report}/reject', [AdminController::class, 'rejectReport'])->name('reports.reject');
            Route::put('/reports/{report}/assign', [AdminController::class, 'assignReport'])->name('reports.assign');

            // Statistics & Analytics
            Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
            Route::get('/export/reports', [AdminController::class, 'exportReports'])->name('export.reports');
            Route::get('/export/users', [AdminController::class, 'exportUsers'])->name('export.users');

            // Task Management
            Route::resource('tasks', TaskController::class)->except(['show']);
        });

    // Routes untuk PETUGAS KEBERSIHAN
    Route::middleware(['role:petugas_kebersihan'])
        ->prefix('petugas')
        ->name('petugas.')
        ->group(function () {
            // Dashboard Petugas
            Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');

            // Assigned Tasks
            Route::get('/tasks', [PetugasController::class, 'tasks'])->name('tasks.index');
            Route::get('/tasks/{task}', [PetugasController::class, 'showTask'])->name('tasks.show');
            Route::put('/tasks/{task}/update', [PetugasController::class, 'updateTask'])->name('tasks.update');
            Route::put('/tasks/{task}/start', [PetugasController::class, 'startTask'])->name('tasks.start');
            Route::put('/tasks/{task}/complete', [PetugasController::class, 'completeTask'])->name('tasks.complete');
            Route::post('/tasks/{task}/progress', [PetugasController::class, 'updateProgress'])->name('tasks.progress');

            // Assigned Reports
            Route::get('/reports', [PetugasController::class, 'assignedReports'])->name('reports.index');
            Route::get('/reports/{report}', [PetugasController::class, 'showReport'])->name('reports.show');
            Route::put('/reports/{report}/update-status', [PetugasController::class, 'updateReportStatus'])->name('reports.update-status');
        });

    // Routes untuk MASYARAKAT (User)
    Route::middleware(['role:masyarakat'])
        ->prefix('user')
        ->name('user.')
        ->group(function () {
            // Dashboard User
            Route::get('/monitoring-sampah', [UserController::class, 'dashboard'])->name('dashboard');
            Route::get('/nabung', [UserController::class, 'nabung'])->name('nabung');
            Route::post('/nabung', [UserController::class, 'store'])->name('nabung.store');
            Route::post('/nabung/calculate', action: [UserController::class, 'calculatePoints'])->name('nabung.calculate');
            Route::get('/riwayat-transaksi', [UserController::class, 'riwayat-transaksi'])->name('riwayat-transaksi');
            Route::get('/saldo-bank', [UserController::class, 'saldo-bank'])->name('saldo-bank');
            Route::get('/tukar-poin', [UserController::class, 'tukarPoin'])->name('tukar-poin');

            // Report Management
            Route::resource('reports', ReportController::class)->only(['index', 'create', 'store', 'show']);
            Route::get('/reports/{report}/track', [ReportController::class, 'trackReport'])->name('reports.track');
        });

    // ===========================================
    // SHARED ROUTES (Multiple Roles)
    // ===========================================

    // Routes yang bisa diakses oleh Petugas Pusat dan Petugas Kebersihan
    Route::middleware(['role:petugas_pusat,petugas_kebersihan'])
        ->prefix('manage')
        ->name('manage.')
        ->group(function () {
            // Notification Management
            Route::get('/notifications', [HomeController::class, 'notifications'])->name('notifications.index');
            Route::put('/notifications/{notification}/read', [HomeController::class, 'markAsRead'])->name('notifications.read');

            // Shared Report Views
            Route::get('/reports/all', [ReportController::class, 'allReports'])->name('reports.all');
            Route::get('/reports/pending', [ReportController::class, 'pendingReports'])->name('reports.pending');
        });

    // Routes yang bisa diakses semua role (kecuali guest)
    Route::prefix('common')
        ->name('common.')
        ->group(function () {
            // Notifications
            Route::get('/notifications', [HomeController::class, 'getNotifications'])->name('notifications');
            Route::post('/notifications/mark-read', [HomeController::class, 'markNotificationsRead'])->name('notifications.mark-read');

            // File Downloads
            Route::get('/download/{file}', [HomeController::class, 'downloadFile'])->name('download');
        });
});

// ===========================================
// API Routes (untuk AJAX calls)
// ===========================================
Route::prefix('api')
    ->middleware('auth')
    ->name('api.')
    ->group(function () {
        // Dashboard Statistics
        Route::get('/dashboard-stats', [HomeController::class, 'getDashboardStats'])->name('dashboard.stats');

        // Search & Filter
        Route::get('/users/search', [AdminController::class, 'searchUsers'])
            ->name('users.search')
            ->middleware('role:petugas_pusat');
        Route::get('/reports/search', [ReportController::class, 'searchReports'])
            ->name('reports.search')
            ->middleware('role:petugas_pusat,petugas_kebersihan');

        // Quick Actions
        Route::post('/reports/{report}/quick-status', [ReportController::class, 'quickStatusUpdate'])
            ->name('reports.quick-status')
            ->middleware('role:petugas_pusat,petugas_kebersihan');
    });

// ===========================================
// FALLBACK & ERROR HANDLING
// ===========================================

// Redirect after login based on role
Route::get('/redirect-dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('petugas_pusat')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('petugas_kebersihan')) {
        return redirect()->route('petugas.dashboard');
    } elseif ($user->hasRole('masyarakat')) {
        return redirect()->route('user.dashboard');
    }

    return redirect()->route('home');
})
    ->middleware('auth')
    ->name('redirect.dashboard');

// Fallback route untuk handling 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
