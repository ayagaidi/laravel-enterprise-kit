@extends('admin.layout')
@section('title', __('app.settings'))
@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}">@csrf @method('PUT')@foreach($settings as $group=>$items)<div class="card mb-4"><div class="card-header bg-white fw-semibold text-capitalize">{{ $group }}</div><div class="card-body"><div class="row g-3">@foreach($items as $setting)<div class="col-md-6"><label class="form-label"><code>{{ $setting->key }}</code></label><input class="form-control" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"><div class="form-text">{{ $setting->type }} · {{ $setting->is_public ? 'public API' : 'private' }}</div></div>@endforeach</div></div></div>@endforeach<button class="btn btn-dark">Save settings</button></form>
@endsection
