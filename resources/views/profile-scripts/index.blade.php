@extends('adminlte::page')

@section('title', 'Profile Scripts')

@section('content_header')
    <h1>Profile Scripts (<a href="{{route('profile-scripts.create')}}">Create New</a>)</h1>
@stop

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
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
                                    <th>Script ID</th>
                                    <th>Script Name</th>
                                    <th>Created</th>
                                    <th>Number of Users</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scripts as $s)
                                    <tr>
                                        <td>{{ $s['id'] }}</td>
                                        <td>
                                                {{ $s['script_name'] }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($s['created_at'])->format('M d, Y h:i A') }}</td>
                                        {{-- <td>{{ \Carbon\Carbon::parse($chat['updated_at'])->format('M d, Y h:i A') }}</td> --}}
                                        <td>
                                            {{$s->dummy_users()->count()}}
                                        </td>
                                        <td>
                                            <form id="{{ "deleteForm-{$s['id']}" }}"
                                                action="{{ route('profile-scripts.delete') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="script_id" value="{{ $s['id'] }}" required>
                                                {{-- <button type="button"
                                                    onclick="confirmWithReason('{{ 'deleteForm-' . $s['id'] }}')"
                                                    class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button> --}}
                                                <button 
                                                onclick="return confirm('Delete this script and its users?')"
                                                class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                            </form>
                                            {{-- @if ($chat['is_archived'])
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
                                        <td colspan="6" class="text-center">No scripts found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{-- </div> --}}
                    </div>

                    @if (count($scripts) > 0)
                        <div class="card-footer clearfix">
                            <div class="float-right">
                                Showing {{ count($scripts) }} conversations
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- <x-confirm-dialog title="Delete this script and its users?" message="Enter reason to perform this action" inputPlaceholder=""
        buttonText="Confirm" cancelText="Cancel" /> --}}
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .badge {
            font-size: 0.85em;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
@stop
