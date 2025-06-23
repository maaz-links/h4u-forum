@extends('adminlte::page')

@section('title', isset($form_eye_color) ? 'Edit Eye Color' : 'Add New Eye Color')

@section('content_header')
    <h1>{{ isset($form_eye_color) ? 'Edit Eye Color' : 'Add New Eye Color' }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ isset($form_eye_color) ? route('form-eye-colors.update', $form_eye_color->id) : route('form-eye-colors.store') }}" method="POST">
                @csrf
                @if(isset($form_eye_color))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="name">Eye Color Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" 
                           value="{{ old('name', $form_eye_color->name ?? '') }}" 
                           placeholder="Enter eye color name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($form_eye_color) ? 'Update' : 'Save' }}
                </button>
                
                @if(isset($form_eye_color) && !$form_eye_color->isDefault())
                    <a href="{{ route('form-eye-colors.setdefault', $form_eye_color->id) }}" class="btn btn-success">Make Default</a>
                @endif

                <a href="{{ route('form-eye-colors.index') }}" class="btn btn-secondary">Back to List</a>
            </form>

            @if(isset($form_eye_color) && !$form_eye_color->isDefault())
                <small class="mt-2 form-text text-muted">
                    When user registers a new profile, the Default Eye Color is used.
                </small>
            @endif
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
