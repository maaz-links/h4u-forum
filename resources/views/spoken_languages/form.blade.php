@extends('adminlte::page')

@section('title', isset($spoken_language) ? 'Edit Spoken Language' : 'Add New Spoken Language')

@section('content_header')
    <h1>{{ isset($spoken_language) ? 'Edit Spoken Language' : 'Add New Spoken Language' }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ isset($spoken_language) ? route('spoken-languages.update', $spoken_language->id) : route('spoken-languages.store') }}" method="POST">
                @csrf
                @if(isset($spoken_language))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="name">Spoken Language Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" 
                           value="{{ old('name', $spoken_language->name ?? '') }}" 
                           placeholder="Enter Spoken Language name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($spoken_language) ? 'Update' : 'Save' }}
                </button>
                <a href="{{ route('spoken-languages.index') }}" class="btn btn-secondary">Back to List</a>
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