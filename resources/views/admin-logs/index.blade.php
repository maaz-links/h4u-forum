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
                    {{-- <div class="card-header">
                        <h3 class="card-title">Admin Logs</h3>
                    </div> --}}
                    <div class="card-body">
                        <table id="logsTable" class="table table-bordered table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Admin</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                                        <td>{{ $log->admin_name }}</td>
                                        <td>{{ $log->action }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No logs available.</td>
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
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#logsTable').DataTable({
                responsive: true,
                order: [[0, 'desc']], // Sort by date descending by default
                columnDefs: [
                    { targets: 0, type: 'date' } // Proper sorting for date column
                ],
                language: {
                    emptyTable: "No logs available",
                    info: "Showing _START_ to _END_ of _TOTAL_ logs",
                    infoEmpty: "Showing 0 to 0 of 0 logs",
                    search: "_INPUT_",
                    searchPlaceholder: "Search logs...",
                    lengthMenu: "Show _MENU_ logs per page"
                }
            });
        });
    </script>
@stop