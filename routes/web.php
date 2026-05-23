<?php

use App\Http\Controllers\CompanyLogoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Onboarding\CompanyOnboardingController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('onboarding/company', [CompanyOnboardingController::class, 'create'])
            ->name('onboarding.company.create');
        Route::post('onboarding/company', [CompanyOnboardingController::class, 'store'])
            ->name('onboarding.company.store');
    });

    Route::middleware(['role:admin', 'company.onboarded'])->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('company/logo', CompanyLogoController::class)->name('company.logo.show');
    });
});

require __DIR__.'/settings.php';
