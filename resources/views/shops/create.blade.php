@extends('adminlte::page')

@section('title', 'Shop')

@section('content_header')
<h1>Create New Shop</h1>
@stop

@section('content')

<div class="container my-5 card px-4 py-4">
  
    <form action="{{ route('shop.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title') }}" placeholder="Enter title here" class="form-control" required>
            @error('title')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" value="{{ old('price') }}" placeholder="Enter price here eg:10" class="form-control" required>
            @error('price')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>


        <div class="mb-3">
            <label>Credits</label>
            <input type="number" name="credits" value="{{ old('credits') }}" placeholder="Enter credits eg:50" class="form-control" required>
            @error('credits')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>

        
        <div class="mb-3">
            <label>Icon</label>
            <input type="file" name="icon" placeholder="Enter credits eg:50" class="form-control">
            @error('icon')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
       
        <button type="submit" class="btn btn-success">Save</button>
        <a class="btn btn-success" href="{{ route('shops.index') }}">Back to List</a>
    </form>
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