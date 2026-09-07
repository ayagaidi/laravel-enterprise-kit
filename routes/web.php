<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.dashboard'));
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'ar'], true), 404);
    session(['locale' => $locale]);

    return back();
})->name('locale');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'active'])->group(function () {
    Route::get('/', DashboardController::class)->middleware('permission:dashboard.view')->name('dashboard');
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update'])
        ->middleware('permission:users.view');
    Route::resource('roles', RoleController::class)->only(['index', 'create', 'store', 'edit', 'update'])
        ->middleware('permission:roles.manage');
    Route::get('settings', [SettingsController::class, 'index'])->middleware('permission:settings.manage')->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->middleware('permission:settings.manage')->name('settings.update');
    Route::get('audit-logs', [AuditLogController::class, 'index'])->middleware('permission:audit.view')->name('audit.index');
});
