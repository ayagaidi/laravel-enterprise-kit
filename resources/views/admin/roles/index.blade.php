@extends('admin.layout')
@section('title', __('app.roles'))
@section('content')
<div class="d-flex justify-content-between mb-3"><div><h1 class="h4">{{ __('app.roles') }}</h1><p class="text-muted">Least-privilege access profiles.</p></div><a class="btn btn-dark" href="{{ route('admin.roles.create') }}">{{ __('app.create_role') }}</a></div><div class="row g-3">@foreach($roles as $role)<div class="col-md-6 col-xl-4"><div class="card h-100"><div class="card-body"><h2 class="h5">{{ $role->name }}</h2><p class="text-muted">{{ $role->description }}</p><div class="small mb-3">{{ $role->users_count }} users · {{ $role->permissions_count }} permissions</div><a class="btn btn-sm btn-outline-dark" href="{{ route('admin.roles.edit',$role) }}">Manage</a></div></div></div>@endforeach</div>
@endsection
