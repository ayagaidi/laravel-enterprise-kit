<?php

use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\SettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/settings/public', SettingsController::class);

    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::get('/me', ProfileController::class);
    });
});
