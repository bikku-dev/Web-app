<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EngineerController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\AuditLogController;

// --- GUEST AUTHENTICATION ROUTES ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// --- PROTECTED ROUTES (LOGGED IN USERS) ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('password.update');
    Route::post('/profile/photo', [AuthController::class, 'updateProfilePhoto'])->name('profile.photo');

    // Dashboard & Manage Menu
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/menu', [DashboardController::class, 'menu'])->name('menu');

    // Complaints & Service Tickets
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create')->middleware('admin');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store')->middleware('admin');
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
    Route::get('/complaints/{complaint}/pdf', [ComplaintController::class, 'downloadPdf'])->name('complaints.pdf');
    Route::get('/complaints/{complaint}/print', [ComplaintController::class, 'printView'])->name('complaints.print');
    Route::get('/complaints/{complaint}/edit', [ComplaintController::class, 'edit'])->name('complaints.edit')->middleware('admin');
    Route::put('/complaints/{complaint}', [ComplaintController::class, 'update'])->name('complaints.update')->middleware('admin');
    Route::post('/complaints/{complaint}/start', [ComplaintController::class, 'startService'])->name('complaints.start');
    Route::post('/complaints/{complaint}/resolve', [ComplaintController::class, 'resolve'])->name('complaints.resolve');
    Route::post('/complaints/{complaint}/cancel', [ComplaintController::class, 'cancel'])->name('complaints.cancel')->middleware('admin');

    // Billing & Invoicing
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/export-csv', [BillingController::class, 'exportCsv'])->name('billing.export')->middleware('admin');
    Route::get('/billing/create', [BillingController::class, 'create'])->name('billing.create');
    Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
    Route::get('/billing/{bill}', [BillingController::class, 'show'])->name('billing.show')->whereNumber('bill');
    Route::get('/billing/{bill}/pdf', [BillingController::class, 'downloadPdf'])->name('billing.pdf')->whereNumber('bill');
    Route::get('/billing/{bill}/print', [BillingController::class, 'printView'])->name('billing.print')->whereNumber('bill');
    Route::post('/billing/{bill}/handover', [BillingController::class, 'verifyHandover'])->name('billing.handover')->middleware('admin')->whereNumber('bill');
    Route::post('/billing/{bill}/credit-collect', [BillingController::class, 'recordCreditCollection'])->name('billing.credit-collect')->whereNumber('bill');

    // Expenses
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::post('/expenses/{expense}/review', [ExpenseController::class, 'review'])->name('expenses.review')->middleware('admin');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockIn');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockOut');
    Route::post('/attendance/{attendance}/review', [AttendanceController::class, 'review'])->name('attendance.review')->middleware('admin');
    Route::get('/attendance/leaderboard', [AttendanceController::class, 'leaderboard'])->name('attendance.leaderboard');

    // Notices
    Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');
    Route::post('/notices/mark-as-read', [NoticeController::class, 'markAsRead'])->name('notices.markAsRead');
    Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store')->middleware('admin');
    Route::post('/notices/{notice}/toggle', [NoticeController::class, 'toggle'])->name('notices.toggle')->middleware('admin');
    Route::delete('/notices/{notice}', [NoticeController::class, 'destroy'])->name('notices.destroy')->middleware('admin');

    // Organizations, Customers & Engineers (accessible for view by authenticated users, manage by admin)
    Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/organizations/{organization}', [OrganizationController::class, 'show'])->name('organizations.show')->whereNumber('organization');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show')->whereNumber('customer');
    Route::get('/engineers', [EngineerController::class, 'index'])->name('engineers.index');
    Route::get('/engineers/create', [EngineerController::class, 'create'])->name('engineers.create')->middleware('admin');
    Route::get('/engineers/{engineer}', [EngineerController::class, 'show'])->name('engineers.show')->whereNumber('engineer');

    // --- ADMIN ONLY ROUTES ---
    Route::middleware('admin')->group(function () {
        // Engineer Management (Admin CRUD & Controls)
        Route::post('/engineers', [EngineerController::class, 'store'])->name('engineers.store');
        Route::get('/engineers/{engineer}/edit', [EngineerController::class, 'edit'])->name('engineers.edit')->whereNumber('engineer');
        Route::put('/engineers/{engineer}', [EngineerController::class, 'update'])->name('engineers.update')->whereNumber('engineer');
        Route::delete('/engineers/{engineer}', [EngineerController::class, 'destroy'])->name('engineers.destroy')->whereNumber('engineer');
        Route::post('/engineers/{engineer}/toggle-status', [EngineerController::class, 'toggleStatus'])->name('engineers.toggle-status')->whereNumber('engineer');
        Route::post('/engineers/{user}/reset-password', [AuthController::class, 'adminResetPassword'])->name('engineers.reset-password')->whereNumber('user');

        // Organization Management
        Route::get('/organizations/create', [OrganizationController::class, 'create'])->name('organizations.create');
        Route::get('/organizations-create', [OrganizationController::class, 'create']);
        Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');
        Route::get('/organizations/{organization}/edit', [OrganizationController::class, 'edit'])->name('organizations.edit')->whereNumber('organization');
        Route::put('/organizations/{organization}', [OrganizationController::class, 'update'])->name('organizations.update')->whereNumber('organization');
        Route::post('/organizations/{organization}/toggle', [OrganizationController::class, 'toggleStatus'])->name('organizations.toggle')->whereNumber('organization');

        // Customer Management
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::get('/customers-create', [CustomerController::class, 'create']);
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit')->whereNumber('customer');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update')->whereNumber('customer');
        Route::post('/customers/{customer}/toggle', [CustomerController::class, 'toggleStatus'])->name('customers.toggle')->whereNumber('customer');

        // Master Data (Areas, Cities, Product Types, Service Types)
        Route::get('/masters/areas', [MasterDataController::class, 'areasIndex'])->name('masters.areas');
        Route::post('/masters/areas', [MasterDataController::class, 'areaStore'])->name('masters.areas.store');
        Route::post('/masters/areas/{area}/toggle', [MasterDataController::class, 'areaToggle'])->name('masters.areas.toggle');

        Route::post('/masters/cities', [MasterDataController::class, 'cityStore'])->name('masters.cities.store');
        Route::post('/masters/cities/{city}/toggle', [MasterDataController::class, 'cityToggle'])->name('masters.cities.toggle');
        Route::get('/masters/cities-by-area/{area}', [MasterDataController::class, 'citiesByArea'])->name('masters.cities.byArea');

        Route::get('/masters/products', [MasterDataController::class, 'productTypesIndex'])->name('masters.products');
        Route::post('/masters/products', [MasterDataController::class, 'productTypeStore'])->name('masters.products.store');
        Route::post('/masters/products/{productType}/toggle', [MasterDataController::class, 'productTypeToggle'])->name('masters.products.toggle');

        Route::get('/masters/services', [MasterDataController::class, 'serviceTypesIndex'])->name('masters.services');
        Route::post('/masters/services', [MasterDataController::class, 'serviceTypeStore'])->name('masters.services.store');
        Route::post('/masters/services/{serviceType}/toggle', [MasterDataController::class, 'serviceTypeToggle'])->name('masters.services.toggle');

        // Audit Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
    });
});
