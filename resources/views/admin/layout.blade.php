<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('app.dashboard')) · Laravel Enterprise Kit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f5f7fb}.shell{min-height:100vh}.sidebar{width:260px;background:#111827;color:#fff}.sidebar a{color:#cbd5e1;text-decoration:none;padding:.7rem .9rem;border-radius:.65rem;display:block}.sidebar a:hover{background:#1f2937;color:#fff}.brand{font-weight:800;letter-spacing:-.02em}.card{border:0;box-shadow:0 4px 20px rgba(15,23,42,.06)}.badge-soft{background:#eef2ff;color:#4338ca}
    </style>
</head>
<body>
<div class="d-flex shell">
    <aside class="sidebar p-3 d-none d-lg-block">
        <div class="brand fs-5 mb-4">Enterprise Kit</div>
        <nav class="d-grid gap-1">
            @if(auth()->user()?->hasPermission('dashboard.view'))<a href="{{ route('admin.dashboard') }}">{{ __('app.dashboard') }}</a>@endif
            @if(auth()->user()?->hasPermission('users.view'))<a href="{{ route('admin.users.index') }}">{{ __('app.users') }}</a>@endif
            @if(auth()->user()?->hasPermission('roles.manage'))<a href="{{ route('admin.roles.index') }}">{{ __('app.roles') }}</a>@endif
            @if(auth()->user()?->hasPermission('settings.manage'))<a href="{{ route('admin.settings.index') }}">{{ __('app.settings') }}</a>@endif
            @if(auth()->user()?->hasPermission('audit.view'))<a href="{{ route('admin.audit.index') }}">{{ __('app.audit') }}</a>@endif
        </nav>
    </aside>
    <main class="flex-grow-1">
        <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
            <strong>@yield('title', __('app.dashboard'))</strong>
            <div class="d-flex gap-2 align-items-center">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('locale', app()->getLocale() === 'ar' ? 'en' : 'ar') }}">{{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}</a>
                <span class="text-muted small">{{ auth()->user()?->name }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-dark">{{ __('app.logout') }}</button></form>
            </div>
        </header>
        <div class="container-fluid p-4">
            @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
            @yield('content')
        </div>
    </main>
</div>
</body></html>
