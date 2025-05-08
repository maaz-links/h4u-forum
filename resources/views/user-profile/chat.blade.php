@extends('adminlte::page')

@section('title', 'Chats')

@section('content_header')
    <h1>Chats</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    {{-- <div class="card-header bg-primary">
                        <h3 class="card-title">Active Conversations</h3>
                    </div> --}}
                    
                    <div class="card-body p-0">
                        {{-- <div class="table-responsive"> --}}
                            <table class="table">
                                <thead>
                                    <tr>
                                        {{-- <th>Chat ID</th> --}}
                                        <th>With User</th>
                                        <th>Created</th>
                                        {{-- <th>Last Updated</th> --}}
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($chats as $chat)
                                    <tr>
                                        {{-- <td>{{ $chat['id'] }}</td> --}}
                                        <td>
                                            <a href="{{ route('user-profile', $chat['other_user']['name']) }}">
                                                {{ $chat['other_user']['name'] }}
                                                 {{-- (ID: {{ $chat['other_user']['user_id'] }}) --}}
                                            </a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($chat['created_at'])->format('M d, Y h:i A') }}</td>
                                        {{-- <td>{{ \Carbon\Carbon::parse($chat['updated_at'])->format('M d, Y h:i A') }}</td> --}}
                                        <td>
                                            @if($chat['unlocked'])
                                                <span class="badge bg-success">Unlocked</span>
                                            @else
                                                <span class="badge bg-warning">Locked</span>
                                            @endif
                                            
                                            @if($chat['is_archived'])
                                                <span class="badge bg-secondary ml-1">Archived</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form id="{{"openchatForm-{$chat['other_user']['name']}"}}" action="{{route('open.conversation')}}" method="POST">
                                                @csrf
                                            <input type="hidden" name="chat_id"
                                            value="{{$chat['id']}}"
                                            required>
                                            <button type="button" onclick="confirmWithReason('{{"openchatForm-".$chat['other_user']['name']}}')" class="btn btn-sm btn-primary">
                                                <i class="fas fa-comments"></i> Open
                                            </button>
                                        </form>
                                            {{-- @if($chat['is_archived'])
                                                <button class="btn btn-sm btn-success unarchive-btn" data-chat-id="{{ $chat['id'] }}">
                                                    <i class="fas fa-inbox"></i> Unarchive
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-warning archive-btn" data-chat-id="{{ $chat['id'] }}">
                                                    <i class="fas fa-archive"></i> Archive
                                                </button>
                                            @endif --}}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No chats found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        {{-- </div> --}}
                    </div>
                    
                    @if(count($chats) > 0)
                    <div class="card-footer clearfix">
                        <div class="float-right">
                            Showing {{ count($chats) }} conversations
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <x-confirm-dialog title="Open conversation?" message="Enter reason to perform this action"
        inputPlaceholder="" buttonText="Confirm" cancelText="Cancel" />
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .badge {
            font-size: 0.85em;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,0.03);
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
@stop