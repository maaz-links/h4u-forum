@extends('adminlte::page')

@section('title', 'Report Details')

@section('content_header')
    <h1>Report Details</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Reporter Information</h5>
                    <p><strong>Name:</strong> <a href='{{route('user-profile',[$report->reporter->name ?? ''])}}'>{{ $report->reporter->name ?? 'N/A' }}</p></a>
                    <p><strong>Email:</strong> {{ $report->reporter->email ?? 'N/A' }}</p>
                    <p><strong>Reported at:</strong> {{ $report->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Reported User</h5>
                    <p><strong>Name:</strong><a href='{{route('user-profile',[$report->reportedUser->name])}}'> {{ $report->reportedUser->name }}</p></a>
                    <p><strong>Email:</strong> {{ $report->reportedUser->email }}</p>
                </div>
            </div>
            
            <div class="mb-4">
                <h5>Reason for Report</h5>
                <div class="p-3 bg-light rounded">
                    {{ $report->reason }}
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">Back to List</a>
                <form action="{{ route('reports.destroy', $report->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this report?')">Delete Report</button>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
<script
  src="https://code.jquery.com/jquery-3.7.1.slim.js"
  integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc="
  crossorigin="anonymous"></script>
@stop