@extends('admin.layout')
@section('title', __('app.users'))
@section('content')
<div class="d-flex justify-content-between mb-3"><div><h1 class="h4 mb-1">{{ __('app.users') }}</h1><p class="text-muted mb-0">Manage identities and role assignments.</p></div>@if(auth()->user()->hasPermission('users.create'))<a class="btn btn-dark" href="{{ route('admin.users.create') }}">{{ __('app.create_user') }}</a>@endif</div>
<div class="card"><div class="table-responsive"><table class="table mb-0 align-middle"><thead><tr><th>Name</th><th>Email</th><th>Roles</th><th>Status</th><th></th></tr></thead><tbody>@foreach($users as $user)<tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>@foreach($user->roles as $role)<span class="badge text-bg-light">{{ $role->name }}</span>@endforeach</td><td><span class="badge {{ $user->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $user->is_active ? __('app.active') : __('app.inactive') }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-dark" href="{{ route('admin.users.edit',$user) }}">Edit</a></td></tr>@endforeach</tbody></table></div><div class="card-body">{{ $users->links() }}</div></div>
@endsection
