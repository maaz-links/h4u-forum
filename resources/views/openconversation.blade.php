@extends('adminlte::page')

@section('title', 'Chat Log #' . $chat->id)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Chat Log #{{ $chat->id }}</h1>
        {{-- <div>
            <a href="" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Chats
            </a>
        </div> --}}
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">Chat Information</h3>
                </div>
                <div class="card-body">
                    <div class="user-info mb-4">
                        <h5>Participants:</h5>
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                @if($chat->user1->profile_picture_id)
                                <img src="{{ 'api/attachments/' . $chat->user1->profile_picture_id }}" 
                                     class="img-circle elevation-2" 
                                     width="50" 
                                     alt="User Image">
                                @else
                                <div class="img-circle elevation-2 bg-secondary d-flex align-items-center justify-content-center" 
                                     style="width:50px; height:50px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                @endif
                            </div>
                            <div>
                                <a href="{{route('user-profile', $chat->user1->name) }}"><strong>{{ $chat->user1->name }}</strong></a><br>
                                {{-- <span class="badge {{ $chat->user1->role == 'HOSTESS' ? 'bg-purple' : 'bg-primary' }}">
                                    {{ ucfirst(strtolower($chat->user1->role)) }}
                                </span> --}}
                                {{-- <small class="text-muted d-block">ID: {{ $chat->user1->id }}</small> --}}
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                @if($chat->user2->profile_picture_id)
                                <img src="{{ 'api/attachments/' . $chat->user2->profile_picture_id }}" 
                                     class="img-circle elevation-2" 
                                     width="50" 
                                     alt="User Image">
                                @else
                                <div class="img-circle elevation-2 bg-secondary d-flex align-items-center justify-content-center" 
                                     style="width:50px; height:50px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                @endif
                            </div>
                            <div>
                                <a href="{{route('user-profile', $chat->user2->name) }}"><strong>{{ $chat->user2->name }}</strong></a><br>
                                {{-- <span class="badge {{ $chat->user2->role == 'HOSTESS' ? 'bg-purple' : 'bg-primary' }}">
                                    {{ ucfirst(strtolower($chat->user2->role)) }}
                                </span> --}}
                                {{-- <small class="text-muted d-block">ID: {{ $chat->user2->id }}</small> --}}
                            </div>
                        </div>
                    </div>
                    
                    {{-- <hr>
                    
                    <div class="chat-meta">
                        <p><strong>Created:</strong> {{ \Carbon\Carbon::parse($chat->created_at)->format('M d, Y h:i A') }}</p>
                        <p><strong>Last Updated:</strong> {{ \Carbon\Carbon::parse($chat->updated_at)->format('M d, Y h:i A') }}</p>
                        <p>
                            <strong>Status:</strong> 
                            <span class="badge {{ $chat->unlocked ? 'bg-success' : 'bg-warning' }}">
                                {{ $chat->unlocked ? 'Unlocked' : 'Locked' }}
                            </span>
                            <span class="badge {{ $chat->is_archived ? 'bg-secondary' : 'bg-primary' }} ml-2">
                                {{ $chat->is_archived ? 'Archived' : 'Active' }}
                            </span>
                        </p>
                    </div> --}}
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card direct-chat direct-chat-primary">
                <div class="card-header">
                    <h3 class="card-title">Message History</h3>
                    <div class="card-tools">
                        <span class="badge bg-primary">{{ count($chat->messages) }} Messages</span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="direct-chat-messages" style="height: auto; min-height: 500px;">
                        @foreach($chat->messages as $message)
                        <div class="direct-chat-msg {{ $message->sender_id == $chat->user1_id ? '' : 'left' }}">
                            <div class="direct-chat-infos clearfix">
                                <span class="direct-chat-name {{ $message->sender_id == $chat->user1_id ? 'float-left' : 'float-left' }}">
                                    {{ $message->sender->name }}
                                    {{-- <span class="badge {{ $message->sender->role == 'HOSTESS' ? 'bg-purple' : 'bg-primary' }} ml-1">
                                        {{ ucfirst(strtolower($message->sender->role)) }}
                                    </span> --}}
                                </span>
                                <span class="direct-chat-timestamp {{ $message->sender_id == $chat->user1_id ? 'float-right' : 'float-right' }}">
                                    {{ \Carbon\Carbon::parse($message->created_at)->format('M d, h:i A') }}
                                </span>
                            </div>
                            
                            {{-- @if($message->sender->profile_picture_id)
                            <img class="direct-chat-img" src="{{ 'api/attachments/' . $message->sender->profile_picture_id }}" alt="User Image">
                            @else
                            <div class="direct-chat-img bg-gray d-flex align-items-center justify-content-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            @endif --}}
                            
                            <div class="direct-chat-texter">
                                {{ $message->message }}
                                {{-- @if(!$message->is_read && $message->sender_id != auth()->id())
                                <span class="float-right"><small class="text-muted">(unread)</small></span>
                                @endif --}}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        {{-- <div class="text-muted">
                            Chat ID: {{ $chat->id }}
                        </div> --}}
                        <div>
                            {{-- <button class="btn btn-default" data-toggle="modal" data-target="#messageDetails">
                                <i class="fas fa-info-circle"></i> Details
                            </button> --}}
                            {{-- @if($chat->is_archived)
                            <form action="" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success"s>
                                    <i class="fas fa-inbox"></i> Unarchive
                                </button>
                            </form>
                            @else
                            <form action="" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-archive"></i> Archive
                                </button>
                            </form>
                            @endif --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Details Modal -->
<div class="modal fade" id="messageDetails" tabindex="-1" role="dialog" aria-labelledby="messageDetailsLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="messageDetailsLabel">Chat Statistics</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="far fa-comments"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Messages</span>
                                <span class="info-box-number">{{ count($chat->messages) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-user-tie"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ $chat->user1->name }} Messages</span>
                                <span class="info-box-number">
                                    {{ $chat->messages->where('sender_id', $chat->user1_id)->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-purple"><i class="fas fa-user-friends"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ $chat->user2->name }} Messages</span>
                                <span class="info-box-number">
                                    {{ $chat->messages->where('sender_id', $chat->user2_id)->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="far fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Duration</span>
                                <span class="info-box-number">
                                    {{ \Carbon\Carbon::parse($chat->created_at)->diffForHumans($chat->updated_at, true) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                @if ($chat->messages->count())
                <h5>First Message</h5>
                <p class="text-muted">
                    {{ $chat->messages->first()->message }}<br>
                    <small>{{ \Carbon\Carbon::parse($chat->messages->first()->created_at)->format('M d, Y h:i A') }}</small>
                </p>
                <h5>Last Message</h5>
                <p class="text-muted">
                    {{ $chat->messages->last()->message }}<br>
                    <small>{{ \Carbon\Carbon::parse($chat->messages->last()->created_at)->format('M d, Y h:i A') }}</small>
                </p>
                @endif
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .direct-chat-messages {
        padding: 10px;
        height: auto;
        overflow-y: auto;
    }
    .direct-chat-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .direct-chat-text {
        border-radius: 0.8rem;
        padding: 0.5rem 1rem;
        
    }
    .direct-chat-msg.right .direct-chat-text {
        
        margin-left: 0;
    }
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    .info-box-icon.bg-purple {
        background-color: #6f42c1 !important;
    }
    /* margin: 0.3rem 0 1.5rem 50px; */

    /* margin-right: 50px; */
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
<script>
    
    $(document).ready(function() {
        // Scroll to bottom of chat
        // $('.direct-chat-messages').scrollTop($('.direct-chat-messages')[0].scrollHeight);
        
        // Archive/unarchive confirmation
        
    });
</script>
@stop