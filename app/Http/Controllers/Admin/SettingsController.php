<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\AuditService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', ['settings' => SystemSetting::query()->orderBy('group')->orderBy('key')->get()->groupBy('group')]);
    }

    public function update(Request $request, SettingsService $settings, AuditService $audit): RedirectResponse
    {
        $payload = $request->validate(['settings' => ['required', 'array'], 'settings.*' => ['nullable', 'string', 'max:2000']]);
        foreach ($payload['settings'] as $key => $value) {
            $current = SystemSetting::query()->where('key', $key)->firstOrFail();
            $old = $current->value;
            $updated = $settings->set($key, $value ?? '');
            if ($old !== $updated->value) $audit->record('setting.updated', $updated, ['value' => $old], ['value' => $updated->value]);
        }
        return back()->with('status', __('app.saved'));
    }
}
