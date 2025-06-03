@extends('adminlte::page')

@section('title', 'Message Alert Details')

@section('content_header')
    <h1>Message Alert Details</h1>
@stop

@section('content')
@if(session('success'))
<div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
</div>
@endif
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">Alert Details</h3>
            </div>
            <div class="card-body">
                {{-- <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>ID:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $alert->id }}
                    </div>
                </div> --}}
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>User:</strong>
                    </div>
                    <div class="col-md-8">
                        <a href="{{ route('user-profile', $alert->user->name) }}">
                            {{ $alert->user->name ?? 'Deleted User' }}
                        </a>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Chat:</strong>
                    </div>
                    <div class="col-md-8">
                        @if($alert->chat)
                        <a href="{{ route('open.conversation', $alert->chat_id) }}">
                            View Full Chat
                        </a>
                        @else
                        Deleted Chat
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Status:</strong>
                    </div>
                    <div class="col-md-8">
                        <span class="badge 
                            @if($alert->status == 'PENDING') bg-warning 
                            @elseif($alert->status == 'APPROVED') bg-success 
                            @elseif($alert->status == 'REJECTED') bg-danger 
                            @else bg-secondary @endif">
                            {{ $alert->status }}
                        </span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Detected Rules:</strong>
                    </div>
                    <div class="col-md-8">
                        @if(is_array($alert->detected_rules))
                            <ul>
                                @foreach($alert->detected_rules as $rule)
                                    <li>{{ $rule }}</li>
                                @endforeach
                            </ul>
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Message Created At:</strong>
                    </div>
                    <div class="col-md-8">
                        {{-- {{ $alert->message_created_at->format('Y-m-d H:i:s') }} --}}
                        {{ $alert->message_created_at->format('F j, Y \a\t g:i A') }}
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Full Message:</strong>
                    </div>
                    <div class="col-md-8">
                        <div class="border p-3 bg-light">
                            {{ $alert->message_body }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">Actions</h3>
            </div>
            <div class="card-body">
                @if(!$alert->isFinalized())
                <form action="{{ route('admin.message-alerts.update', $alert->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-3">
                        <label for="status">Change Status</label>
                        <select name="status" id="status" class="form-control">
                            {{-- <option value="PENDING" {{ $alert->status == 'PENDING' ? 'selected' : '' }}>PENDING</option> --}}
                            <option value="REJECTED" {{ $alert->status == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                            <option value="APPROVED" {{ $alert->status == 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                            <option value="ARCHIVED" {{ $alert->status == 'ARCHIVED' ? 'selected' : '' }}>ARCHIVED</option>
                        </select>
                    </div>
                    
                    {{-- <div class="form-group mb-3">
                        <label for="notes">Admin Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                    </div> --}}
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </form>
                
                <hr>
                @endif
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.ban.show', $alert->user->name) }}" class="btn btn-danger">
                        <i class="fas fa-ban"></i> Manage User Ban
                    </a>
                    <a href="{{ route('admin.message-alerts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    
                    {{-- @if($alert->chat_id)
                    <a href="{{ route('admin.chats.messages', $alert->chat_id) }}" class="btn btn-secondary">
                        <i class="fas fa-search"></i> View Full Chat
                    </a>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .badge {
        font-size: 1em;
        padding: 0.5em 0.75em;
    }
</style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
@stop