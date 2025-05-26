{{-- resources/views/admin/email-templates/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Templates Management')

@section('content_header')
    <h1>Templates</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">Available Templates</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $type => $template)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                            <td>{{ $subjects[$type] }}</td>
                            <td>
                                <a href="{{ route('admin.email-templates.edit', $type) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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