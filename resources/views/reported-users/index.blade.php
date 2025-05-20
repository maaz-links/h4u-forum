@extends('adminlte::page')

@section('title', 'Reports Management')

@section('content_header')
    <h1>Users Reported</h1>
@stop

@section('content')
    @if (session('success'))
            <div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        {{-- <th>ID</th> --}}
                        <th>Reporter</th>
                        <th>Reported User</th>
                        {{-- <th>Reason</th> --}}
                        <th>Reported At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                    <tr>
                        {{-- <td>{{ $report->id }}</td> --}}
                        @if($report->reporter)
                            <td><a href='{{route('user-profile',[$report->reporter->name])}}'>{{ $report->reporter->name }}</a></td>
                        @else
                            <td>N/A</td>
                        @endif
                        @if($report->reportedUser)
                            <td><a href='{{route('user-profile',[$report->reportedUser->name])}}'>{{ $report->reportedUser->name }}</a></td>
                        @else
                            <td>N/A</td>
                        @endif
                        {{-- <td>{{ Str::limit($report->reason, 50) }}</td> --}}
                        <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            {{-- <a href="{{ route('reports.show', $report->id) }}" class="btn btn-sm btn-info">View</a> --}}
                            <a href="#" class="btn btn-sm btn-info view-report" data-reporter="{{ $report->reporter->name ?? 'N/A' }}" data-reason="{{ $report->reason }}">Reason</a>
                            <form action="{{ route('reports.destroy', $report->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
        <div class="card-footer">
            {{ $reports->links() }}
        </div>
        @endif
    </div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
@stop

@section('js')
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
<script
  src="https://code.jquery.com/jquery-3.7.1.slim.js"
  integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc="
  crossorigin="anonymous"></script>

  <script>
    $(document).ready(function() {
        // Handle view report button click
        $('.view-report').click(function(e) {
            e.preventDefault();
            const reason = $(this).data('reason');
            const reporter = $(this).data('reporter');
            
            Swal.fire({
                title: `Report Reason by ${reporter}`,
                text: reason,
                // icon: 'info',
                confirmButtonText: 'OK',
                width: '600px',
                customClass: {
                    content: 'text-left'
                }
            });
        });
    });
    </script>
@stop