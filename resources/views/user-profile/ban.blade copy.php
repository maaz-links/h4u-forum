@extends('adminlte::page')

@section('title', 'Ban Management - ' . $user->name)

@section('content_header')
    <h1>Ban Management: <a href="{{ route('user-profile', $user->name) }}" class="">{{ $user->name }}</a></h1>
    
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
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-{{ $user->bans ? 'danger' : 'success' }}">
                    <h3 class="card-title">Current Status</h3>
                </div>
                <div class="card-body">
                    @if($user->isBanned())
                        @php $ban = $user->activeBan(); @endphp
                        <div class="alert alert-danger">
                            <h4><i class="fas fa-ban"></i> User is Banned</h4>
                            <p><strong>Type:</strong> {{ $ban->isPermanent() ? 'Permanent Ban' : 'Temporary Ban' }}</p>
                            {{-- <p><strong>Reason:</strong> {{ $ban->reason ?? 'No reason provided' }}</p> --}}
                            @if($ban->isTemporary())
                                <p><strong>Expires:</strong> {{ $ban->expired_at }}</p>
                            @endif
                            <p><strong>Banned On:</strong> {{ $ban->created_at }}</p>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <h4><i class="fas fa-check-circle"></i> User is Not Banned</h4>
                            <p>This user currently has full access to the system.</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Warnings</h3>
                   
                </div>
                <div class="card-body">
                    <p>Warning Count: {{$user->profile()->value('warnings')}}</p>
                    <form method="POST" action="{{ route('admin.users.warn', $user->id) }}">
                        @csrf
                        {{-- <div class="form-group">
                            <label for="permanent_reason">Permanent Ban Reason</label>
                            <textarea class="form-control" id="permanent_reason" name="reason" rows="2" required></textarea>
                        </div> --}}
                        <button type="submit" onclick="return confirm('Confirm this action?')" class="btn btn-warning btn-block mt-2">
                            <i class="fas fa-exclamation-triangle "></i> Send Warning to this user
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Ban Actions</h3>
                </div>
                <div class="card-body">
                    @if($user->bans)
                        <form method="POST" action="{{ route('admin.users.unban', $user->id) }}">
                            @csrf
                            <button type="submit" onclick="return confirm('Confirm this action?')" class="btn btn-success btn-block mb-3">
                                <i class="fas fa-check"></i> Remove Ban
                            </button>
                        </form>
                    @endif

                    @if(!$user->bans)
                        <form method="POST" action="{{ route('admin.users.ban', $user->id) }}">
                            @csrf
                            {{-- <div class="form-group">
                                <label for="permanent_reason">Permanent Ban Reason</label>
                                <textarea class="form-control" id="permanent_reason" name="reason" rows="2" required></textarea>
                            </div> --}}
                            <button type="submit" onclick="return confirm('Confirm this action?')" class="btn btn-danger btn-block mt-2">
                                <i class="fas fa-ban"></i> Permanent Ban
                            </button>
                        </form>
                   
                    <hr>

                    <form method="POST" action="{{ route('admin.users.temp-ban', $user->id) }}">
                        @csrf
                        {{-- <div class="form-group">
                            <label for="temp_reason">Temporary Ban Reason</label>
                            <textarea class="form-control" id="temp_reason" name="reason" rows="2" required></textarea>
                        </div> --}}
                        <div class="form-group mt-2">
                            <label for="days">Ban Duration</label>
                            <select class="form-control" id="days" name="days">
                                <option value="1">1 Day</option>
                                <option value="3">3 Days</option>
                                <option value="7" selected>1 Week</option>
                                <option value="14">2 Weeks</option>
                                <option value="30">1 Month</option>
                            </select>
                        </div>
                        <button type="submit" onclick="return confirm('Confirm this action?')" class="btn btn-warning btn-block mt-2">
                            <i class="fas fa-clock"></i> Temporary Ban
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@stop

@section('css')
<style>
    .card-header {
        color: white;
    }
    .table th {
        background-color: #f8f9fa;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .card-header {
            color: white;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js"
        integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
@stop