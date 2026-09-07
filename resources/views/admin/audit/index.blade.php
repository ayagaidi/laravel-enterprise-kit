@extends('admin.layout')
@section('title', __('app.audit'))
@section('content')
<form class="row g-2 mb-3"><div class="col-md-4"><input class="form-control" name="action" value="{{ request('action') }}" placeholder="Filter by action…"></div><div class="col-auto"><button class="btn btn-outline-dark">Filter</button></div></form><div class="card"><div class="table-responsive"><table class="table mb-0 align-middle"><thead><tr><th>Time</th><th>Action</th><th>Actor</th><th>Subject</th><th>Request ID</th></tr></thead><tbody>@foreach($logs as $log)<tr><td class="text-nowrap">{{ $log->created_at }}</td><td><code>{{ $log->action }}</code></td><td>{{ $log->actor?->email ?? 'System' }}</td><td>{{ class_basename($log->subject_type ?? '') }} #{{ $log->subject_id }}</td><td><small class="text-muted">{{ $log->request_id }}</small></td></tr>@endforeach</tbody></table></div><div class="card-body">{{ $logs->links() }}</div></div>
@endsection
