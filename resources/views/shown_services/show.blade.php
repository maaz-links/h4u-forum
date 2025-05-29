@extends('adminlte::page')

@section('title', 'Service Details')

@section('content_header')
    <h1>Service Details</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="20%">Name</th>
                    <td>{{ $shownService->name }}</td>
                </tr>
                <tr>
                    <th>Display Order</th>
                    <td>{{ $shownService->display_order }}</td>
                </tr>
                <tr>
                    <th>Image</th>
                    <td>
                        @if($shownService->image_path)
                            <img src="{{ asset('storage/'.$shownService->image_path) }}" alt="{{ $shownService->name }}" style="max-width: 300px;">
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $shownService->created_at->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $shownService->updated_at->format('d M Y H:i') }}</td>
                </tr>
            </table>
            
            <div class="mt-3">
                <a href="{{ route('shown-services.index') }}" class="btn btn-default">Back to List</a>
                <a href="{{ route('shown-services.edit', $shownService->id) }}" class="btn btn-warning">Edit</a>
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
