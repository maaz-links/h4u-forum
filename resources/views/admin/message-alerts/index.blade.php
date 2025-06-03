@extends('adminlte::page')

@section('title', 'Message Alerts')

@section('content_header')
    <h1>Message Alerts</h1>
@stop

@section('content')
@if(session('success'))
<div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
</div>
@endif
<div class="card">
    <div class="card-header bg-primary">
        <h3 class="card-title">All Message Alerts</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    {{-- <th>ID</th> --}}
                    <th>User</th>
                    
                    <th>Message Preview</th>
                    <th>Detected Rules</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alerts as $alert)
                <tr>
                    {{-- <td>{{ $alert->id }}</td> --}}
                    <td>
                        {{-- <a href="{{ route('admin.users.show', $alert->user_id) }}"> --}}
                            {{ $alert->user->name ?? 'Deleted User' }}
                        {{-- </a> --}}
                    </td>
                    <td>{{ Str::limit($alert->message_body, 50) }}</td>
                    <td>
                        @if(is_array($alert->detected_rules))
                            {{ implode(', ', $alert->detected_rules) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        <span class="badge 
                            @if($alert->status == 'PENDING') bg-warning 
                            @elseif($alert->status == 'APPROVED') bg-success 
                            @elseif($alert->status == 'REJECTED') bg-danger 
                            @else bg-secondary @endif">
                            {{ $alert->status }}
                        </span>
                    </td>
                    <td>{{ $alert->message_created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.message-alerts.show', $alert->id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-3">
            {{ $alerts->links() }}
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .badge {
        font-size: 0.9em;
    }
</style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
@stop