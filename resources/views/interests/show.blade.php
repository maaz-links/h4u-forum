@extends('adminlte::page')

@section('title', 'Interest Details')

@section('content_header')
    <h1>Interest Details</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <td>{{ $interest->id }}</td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{ $interest->name }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $interest->created_at->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $interest->updated_at->format('d M Y H:i') }}</td>
                </tr>
            </table>
            
            <div class="mt-3">
                <a href="{{ route('interests.index') }}" class="btn btn-default">Back</a>
            </div>
        </div>
    </div>
@stop