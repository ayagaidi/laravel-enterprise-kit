<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", 3600, function () use ($key, $default) {
            $setting = SystemSetting::query()->where('key', $key)->first();

            return $setting ? $this->cast($setting->value, $setting->type) : $default;
        });
    }

    public function set(string $key, mixed $value): SystemSetting
    {
        $setting = SystemSetting::query()->where('key', $key)->firstOrFail();
        $setting->update(['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]);
        Cache::forget("setting:{$key}");

        return $setting->refresh();
    }

    private function cast(?string $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOL),
            'integer' => (int) $value,
            'json' => json_decode($value ?: 'null', true),
            default => $value,
        };
    }
}
