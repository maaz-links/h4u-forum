@extends('adminlte::page')

@section('title', 'Admin Logs')

@section('content_header')
    <h1>Admin Logs</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Admin Logs</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    {{-- <th>ID</th> --}}
                                    <th style='width: 20%'>Date</th>
                                    <th style='width: 20%'>Admin</th>
                                    <th style='width: 60%'>Action</th>
                                    {{-- <th style='width: 40%'>Reason</th> --}}
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr>
                                        {{-- <td>{{ $log->id }}</td> --}}

                                        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                                        <td>{{ $log->admin_name }}</td>
                                        <td>{{ $log->action }}</td>
                                        {{-- <td>{{ $log->reason }}</td> --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No logs available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
@stop
