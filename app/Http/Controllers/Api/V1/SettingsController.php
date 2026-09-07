<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(['data' => SystemSetting::query()->where('is_public', true)->pluck('value', 'key')]);
    }
}
