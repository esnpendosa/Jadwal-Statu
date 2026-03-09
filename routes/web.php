<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleCalendarController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RiskRuleController;
use App\Http\Controllers\Admin\AiSuggestionRuleController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\SetLocale;

// ==============================
// AUTH ROUTES
// ==============================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registration
    Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistration'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ==============================
// AUTHENTICATED ROUTES
// ==============================
Route::middleware(['auth', SetLocale::class])->group(function () {
    Route::get('lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/dashboard/ai-sync', [DashboardController::class, 'runAiAnalysis'])->name('dashboard.ai-sync');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update_password');
    Route::put('/profile/language', [ProfileController::class, 'updateLanguage'])->name('profile.language');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Google Calendar OAuth (Admin only)
    Route::middleware('role:Admin')->group(function () {
        Route::get('/auth/google/calendar', [GoogleCalendarController::class, 'redirect'])->name('google.calendar.redirect');
        Route::get('/auth/google/callback', [GoogleCalendarController::class, 'callback'])->name('google.calendar.callback');
        Route::delete('/auth/google/disconnect', [GoogleCalendarController::class, 'disconnect'])->name('google.calendar.disconnect');
    });

    // ==============================
    // INVENTORY
    // ==============================
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/{inventory}', [InventoryController::class, 'show'])->name('show');
        Route::get('/{inventory}/history', [InventoryController::class, 'history'])->name('history');

        // Admin only
        Route::middleware('role:Admin')->group(function () {
            Route::get('/create', [InventoryController::class, 'create'])->name('create');
            Route::post('/', [InventoryController::class, 'store'])->name('store');
            Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('edit');
            Route::put('/{inventory}', [InventoryController::class, 'update'])->name('update');
            Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('destroy');
            Route::post('/{inventory}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('adjust-stock');
            Route::post('/{inventory}/adjust', [InventoryController::class, 'adjustStock'])->name('adjust');
        });
    });

    // Categories (Admin only)
    Route::prefix('categories')->name('categories.')->middleware('role:Admin')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // ==============================
    // PROJECTS
    // ==============================
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');

        // Admin only
        Route::middleware('role:Admin')->group(function () {
            Route::get('/create', [ProjectController::class, 'create'])->name('create');
            Route::post('/', [ProjectController::class, 'store'])->name('store');
            Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
            Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
            Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
            Route::post('/{project}/complete', [ProjectController::class, 'complete'])->name('complete');
        });
    });

    // ==============================
    // BORROW
    // ==============================
    Route::prefix('borrow')->name('borrow.')->group(function () {
        Route::get('/', [BorrowController::class, 'index'])->name('index');
        Route::get('/{borrow}', [BorrowController::class, 'show'])->name('show');
        Route::get('/{borrow}/print', [BorrowController::class, 'print'])->name('print');

        // PIC & Admin dapat membuat borrow
        Route::get('/create', [BorrowController::class, 'create'])->name('create');
        Route::post('/', [BorrowController::class, 'store'])->name('store');

        // Admin only — approve/reject
        Route::middleware('role:Admin')->group(function () {
            Route::post('/{borrow}/approve', [BorrowController::class, 'approve'])->name('approve');
            Route::post('/{borrow}/reject', [BorrowController::class, 'reject'])->name('reject');
            Route::post('/{borrow}/notify', [BorrowController::class, 'sendNotification'])->name('notify');
        });
    });

    // ==============================
    // RETURN
    // ==============================
    Route::prefix('return')->name('return.')->group(function () {
        Route::get('/', [ReturnController::class, 'index'])->name('index');
        Route::get('/create/{borrow}', [ReturnController::class, 'create'])->name('create');
        Route::post('/{borrow}', [ReturnController::class, 'store'])->name('store');
        Route::get('/{return}', [ReturnController::class, 'show'])->name('show');
        Route::get('/{return}/print', [ReturnController::class, 'print'])->name('print');

        // Admin only — verify return
        Route::post('/{return}/verify', [ReturnController::class, 'verify'])->name('verify')->middleware('role:Admin');
    });

    // ==============================
    // REPORTS (Admin only)
    // ==============================
    Route::prefix('reports')->name('reports.')->middleware('role:Admin')->group(function () {
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/borrow', [ReportController::class, 'borrow'])->name('borrow');
        Route::get('/risk', [ReportController::class, 'risk'])->name('risk');
        Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
    });

    // ==============================
    // ADMIN PANEL (Admin only)
    // ==============================
    Route::prefix('admin')->name('admin.')->middleware('role:Admin')->group(function () {

        // Users
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Roles & Permissions
        Route::resource('roles', RoleController::class);
        Route::post('/roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.permissions');

        // Risk Rules
        Route::resource('risk-rules', RiskRuleController::class);
        Route::post('/risk-rules/{rule}/toggle', [RiskRuleController::class, 'toggle'])->name('risk-rules.toggle');

        // AI Suggestion Rules
        Route::resource('ai-rules', AiSuggestionRuleController::class);
        Route::post('/ai-rules/{rule}/toggle', [AiSuggestionRuleController::class, 'toggle'])->name('ai-rules.toggle');

        // System Settings
        Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SystemSettingController::class, 'update'])->name('settings.update');

        // Email Templates
        Route::resource('email-templates', EmailTemplateController::class);

        // Audit Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        // Google Calendar Admin shortcuts
        Route::get('/google/auth', function () {
            return redirect()->route('google.calendar.redirect');
        })->name('google.auth');
        Route::post('/google/revoke', function () {
            return redirect()->route('google.calendar.disconnect');
        })->name('google.revoke');
    });
});
