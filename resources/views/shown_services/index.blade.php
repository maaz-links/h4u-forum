@extends('adminlte::page')

@section('title', 'Shown Services')

@section('content_header')
    <h1>Shown Services</h1>
@stop

@section('content')
    <div class="card">
        {{-- <div class="card-header">
            <a href="{{ route('shown-services.create') }}" class="btn btn-primary">Add New Service</a>
        </div> --}}
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        {{-- <th width="5%">Order</th> --}}
                        <th width="20%">Image</th>
                        <th width="15%">Name</th>
                        <th width="25%">Description</th>
                        <th width="40%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                    <tr>
                        {{-- <td>{{ $service->display_order }}</td> --}}
                        <td>
                            @if($service->image_path)
                                <img src="{{ asset('storage/'.$service->image_path) }}" alt="{{ $service->name }}" style="max-width: 100px;">
                            @endif
                        </td>
                        <td>{{ $service->name }}</td>

                        <td>{{ $service->description ?? 'N/A' }}</td>
                        <td>
                            {{-- <a href="{{ route('shown-services.show', $service->id) }}" class="btn btn-sm btn-info">View</a> --}}
                            <a href="{{ route('shown-services.edit', $service->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            {{-- <form action="{{ route('shown-services.destroy', $service->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form> --}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
