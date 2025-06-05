@extends('adminlte::page')

@section('title', 'Create Moderation Rule')

@section('content_header')
    <h1>Create Moderation Rule</h1>
@stop

@section('content')
    <form action="{{ route('moderation-rules.store') }}" method="POST">
        @csrf
        @include('moderation-rules.form')
        <div>
            <button type="submit" class="btn btn-primary">Create Rule</button>
            <a href="{{ route('moderation-rules.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </form>
@stop
@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
@stop