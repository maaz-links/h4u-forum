{{-- resources/views/admin/email-templates/edit.blade.php --}}
@extends('adminlte::page')

@section('title', 'Edit Template')

@section('content_header')
    <h1>Edit Template - {{ ucfirst(str_replace('_', ' ', $type)) }}</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">Template Details</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.email-templates.update', $type) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" 
                           value="{{ old('subject', $subject) }}" required>
                </div>
                
                <div class="form-group">
                    <label for="template">Body</label>
                    <textarea class="form-control" id="template" name="template" rows="15" required>{{ old('template', $template) }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Template</button>
                <a href="{{ route('admin.email-templates.index') }}" class="btn btn-default">Cancel</a>
            </form>
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