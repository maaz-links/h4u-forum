@extends('adminlte::page')

@section('title', isset($interest) ? 'Edit Interest' : 'Add New Interest')

@section('content_header')
    <h1>{{ isset($interest) ? 'Edit Interest' : 'Add New Interest' }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ isset($interest) ? route('interests.update', $interest->id) : route('interests.store') }}" method="POST">
                @csrf
                @if(isset($interest))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="name">Interest Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" 
                           value="{{ old('name', $interest->name ?? '') }}" 
                           placeholder="Enter interest name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($interest) ? 'Update' : 'Save' }}
                </button>
                <a href="{{ route('interests.index') }}" class="btn btn-secondary">Back to List</a>
            </form>
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