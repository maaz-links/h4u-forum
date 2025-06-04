@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
{{-- <div class="card">
    <div class="card-body p-0"> --}}
        @if (session('success'))
    <div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
<form method="GET" action="{{ route('home') }}" class="mb-3">
    <div class="input-group">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            class="form-control"
            placeholder="Search by Username"
        >
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>
        <table id="usersTable" class="table table-striped">
            <thead>
                <tr>
                    <th>S/L</th>
                    <th>Role</th>
                    <th>Username</th>
                    {{-- <th>Verified Profile</th>
                    <th>Top Profile</th> --}}
                    <th>Credits</th>
                    <th>Joined</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <th>{{$loop->index + $users->firstItem()}}</th>
                        <td>
                            @if ($user->role == \App\Models\User::ROLE_HOSTESS)
                                Hostess
                            @elseif($user->role == \App\Models\User::ROLE_KING)
                                King
                            @endif
                        </td>
                        <td>{{ $user->name }}</td>
                        {{-- <td>
                            @if ($user->profile->verified_profile)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            @if ($user->profile->top_profile)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td> --}}
                        <td>{{ $user->profile->credits }} {!!($user->role == \App\Models\User::ROLE_HOSTESS) ? 'Free Messages':'<i class="fas fa-coins"></i> '!!}</td>
                        <td>{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</td>
                        <td>
                            <a href="{{route('user-profile',[$user->name])}}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-3">
            {{ $users->links() }}
        </div>
    {{-- </div> --}}
{{-- </div> --}}
{{-- @php

echo extension_loaded('gd') ? 'GD is loaded' : 'GD is NOT loaded';
phpinfo();
@endphp --}}
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
@stop

@section('js')
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js"
        integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
        {{-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#usersTable').DataTable();
    });
</script> --}}

@stop