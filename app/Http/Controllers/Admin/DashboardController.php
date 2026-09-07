<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'roles' => Role::query()->count(),
                'permissions' => Permission::query()->count(),
                'settings' => SystemSetting::query()->count(),
            ],
            'recentAuditLogs' => AuditLog::query()->with('actor')->latest('created_at')->limit(8)->get(),
        ]);
    }
}
