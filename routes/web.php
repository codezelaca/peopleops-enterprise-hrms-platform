<?php

use App\Http\Controllers\Admin\AccessController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CompanyLogoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Onboarding\CompanyOnboardingController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'account.active', 'verified', 'record.activity'])->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('onboarding/company', [CompanyOnboardingController::class, 'create'])
            ->name('onboarding.company.create');
        Route::post('onboarding/company', [CompanyOnboardingController::class, 'store'])
            ->name('onboarding.company.store');
    });

    Route::middleware(['role:admin', 'company.onboarded'])->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('company/logo', CompanyLogoController::class)->name('company.logo.show');

        Route::get('admin/users', AccessController::class)->name('admin.users.index');
        Route::get('admin/audit-logs', AuditLogController::class)->name('admin.audit-logs.index');
        Route::post('admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::put('admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        Route::post('admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');
        Route::put('admin/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
        Route::delete('admin/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');

        Route::post('admin/permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');
        Route::put('admin/permissions/{permission}', [PermissionController::class, 'update'])->name('admin.permissions.update');
        Route::delete('admin/permissions/{permission}', [PermissionController::class, 'destroy'])->name('admin.permissions.destroy');
    });
});

require __DIR__.'/settings.php';
