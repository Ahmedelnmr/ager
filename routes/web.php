<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\RentScheduleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Public: redirect root to dashboard or login ──────────────────────
Route::get('/', fn() => redirect()->route('dashboard'));

Route::get('/seed-db', function () {
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    return 'Database seeded successfully! You can now log in.';
});

// ── Authenticated routes ──────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Buildings
    Route::resource('buildings', BuildingController::class);

    // Units
    Route::resource('units', UnitController::class);

    // Tenants
    Route::resource('tenants', TenantController::class);

    // Contracts
    Route::resource('contracts', ContractController::class);
    Route::patch('contracts/{contract}/terminate', [ContractController::class, 'terminate'])
        ->name('contracts.terminate');

    // Rent Schedules
    Route::get('rent-schedules',                   [RentScheduleController::class, 'index'])->name('rent-schedules.index');
    Route::get('rent-schedules/{rentSchedule}',    [RentScheduleController::class, 'show'])->name('rent-schedules.show');
    Route::patch('rent-schedules/{rentSchedule}',  [RentScheduleController::class, 'update'])->name('rent-schedules.update');

    // Payments
    Route::get('payments',                         [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/schedule/{schedule}',     [PaymentController::class, 'create'])->name('payments.create');
    Route::post('payments/schedule/{schedule}',    [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}/receipt',       [PaymentController::class, 'receipt'])->name('payments.receipt');
    Route::get('payments/{payment}/download',      [PaymentController::class, 'downloadReceipt'])->name('payments.download-receipt');

    // Maintenance
    Route::resource('maintenance', MaintenanceController::class);

    // Notifications
    Route::get('notifications',            [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read',   [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

    // Reports
    Route::get('reports',                  [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export-payments',  [ReportController::class, 'exportPayments'])->name('reports.export-payments');
    Route::get('reports/export-contracts', [ReportController::class, 'exportContracts'])->name('reports.export-contracts');

    // Users (admin/owner only)
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::get('audit-log', [UserController::class, 'auditLog'])->name('audit.index');
});

require __DIR__.'/auth.php';
