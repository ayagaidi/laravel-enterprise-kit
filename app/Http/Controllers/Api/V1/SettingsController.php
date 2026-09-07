<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class SettingsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $data = [];

        foreach (SystemSetting::query()->where('is_public', true)->orderBy('key')->get(['key', 'value']) as $setting) {
            Arr::set($data, $setting->key, $setting->value);
        }

        return response()->json(['data' => $data]);
    }
}
