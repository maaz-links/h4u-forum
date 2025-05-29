@extends('adminlte::page')

@section('title', 'Interests')

@section('content_header')
    <h1>Interests</h1>
@stop

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="float-right">
                <a href="{{ route('interests.create') }}" class="btn btn-primary">Add New Interest</a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="60%">Name</th>
                                <th width="40%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($interests->take(ceil(count($interests)/2)) as $interest)
                            <tr>
                                <td>{{ $interest->name }}</td>
                                <td>
                                    <a href="{{ route('interests.edit', $interest->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('interests.destroy', $interest->id) }}" method="POST" style="display: inline-block;">
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
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="60%">Name</th>
                                <th width="40%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($interests->slice(ceil(count($interests)/2)) as $interest)
                            <tr>
                                <td>{{ $interest->name }}</td>
                                <td>
                                    <a href="{{ route('interests.edit', $interest->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('interests.destroy', $interest->id) }}" method="POST" style="display: inline-block;">
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
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js"
        integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
@stop